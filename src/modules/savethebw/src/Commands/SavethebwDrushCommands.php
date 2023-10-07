<?php

namespace Drupal\savethebw\Commands;

use Drupal\Core\Database\Database;
use Drupal\Core\File\FileSystemInterface;
use Drupal\node\Entity\Node;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\path_alias\Entity\PathAlias;
use Drupal\pathauto\PathautoState;
use Drush\Commands\DrushCommands;

class SavethebwDrushCommands extends DrushCommands {
  /**
   * Migrate blog images from wysiwyg fields from D7 site
   *
   * @command savethebw:migrate_blog_wysiwyg_images
   */
  public function migrate_blog_wysiwyg_images() {
    $node_storage = \Drupal::entityTypeManager()->getStorage('node');
    $query = \Drupal::entityQuery('node');
    $query->condition('type', [ 'blog' ], 'IN');
    $nids = array_values($query->execute());
    foreach ($nids as $nid) {
      $node = $node_storage->load($nid);
      foreach ($node->get('field_components')->referencedEntities() as $paragraph) {
        if ($paragraph->bundle() == 'text') {
          $doc = new \DOMDocument();
          @$doc->loadHTML($paragraph->field_text->value);
          $imageTags = $doc->getElementsByTagName('img');
          $srces = [];
          foreach ($imageTags as $image) {
            print $image->getAttribute('src') . "\n";
          }
        }
      }
    }
  }

  /**
   * Migrate blog posts from D7 site
   *
   * @command savethebw:migrate_blog_posts
   */
  public function migrate_blog_posts() {
    Database::setActiveConnection('migrate');
    $database = Database::getConnection();
    Database::setActiveConnection();
    $query = <<<EOT
      SELECT
        node.nid, node.vid, node.title, node.created, node.changed, node.type,
        field_data_field_body.field_body_value, field_data_field_date.field_date_value,
        field_data_field_post.field_post_value, url_alias.alias, url_alias.source,
        field_data_field_image.field_image_fid, field_data_field_image.field_image_alt,
        field_data_field_image.delta, node.status
      FROM node
      LEFT JOIN field_data_field_body
        ON node.nid = field_data_field_body.entity_id AND field_data_field_body.revision_id = node.vid
      LEFT JOIN field_data_field_date
        ON node.nid = field_data_field_date.entity_id AND field_data_field_date.revision_id = node.vid
      LEFT JOIN field_data_field_post
        ON node.nid = field_data_field_post.entity_id AND field_data_field_post.revision_id = node.vid
      LEFT JOIN field_data_field_image
        ON node.nid = field_data_field_image.entity_id AND field_data_field_image.revision_id = node.vid
      LEFT JOIN url_alias
        ON url_alias.source = concat('node/', node.nid)
      WHERE node.type = 'blog_post'
      AND created > UNIX_TIMESTAMP('2021-07-21')
    EOT;
    $blogs = $database->query($query)->fetchAll();
    foreach ($blogs as $blog) {
      $path_alias_repository = \Drupal::service('path_alias.repository');
      if ($path_alias_repository->lookupByAlias('/' . $blog->alias, 'en')) {
        print_r('Skipping ' . $blog->alias . "\n");
        continue;
      } else {
        print_r('Not skipping ' . $blog->alias . "\n");
      }
      if (is_numeric($blog->delta) && $blog->delta > 0) {
        continue;
      }
      $query = <<<EOT
        SELECT
          field_data_field_blog_category.field_blog_category_tid,
          taxonomy_term_data.name
        FROM field_data_field_blog_category
        LEFT JOIN taxonomy_term_data
          ON field_data_field_blog_category.field_blog_category_tid = taxonomy_term_data.tid
        WHERE field_data_field_blog_category.entity_id = {$blog->nid}
          AND field_data_field_blog_category.revision_id = {$blog->vid}
      EOT;
      $terms = $database->query($query)->fetchAll();
      $field_department = [];
      foreach ($terms as $term) {
        $d9_terms = \Drupal::entityTypeManager()
          ->getStorage('taxonomy_term')
          ->loadByProperties([ 'name' => $term->name, 'vid' => 'department' ]);
        foreach ($d9_terms as $d9_term) {
          $field_department[] = [ 'target_id' => $d9_term->id() ];
        }
      }
      $field_image = NULL;
      if ($blog->field_image_fid) {
        $query = <<<EOT
          SELECT
            file_managed.uri
          FROM file_managed
          WHERE file_managed.fid = {$blog->field_image_fid}
        EOT;
        $files = $database->query($query)->fetchAll();
        if (count($files) > 0) {
          $path = \Drupal::service('file_system')->realpath($files[0]->uri);
          $data = file_get_contents($path);
          $file = file_save_data($data, $files[0]->uri, FileSystemInterface::EXISTS_REPLACE);
          $field_image = [
            'target_id' => $file->id(),
            'alt' => $blog->field_image_alt,
          ];
        }
      }
      $paragraph = Paragraph::create([
        'type' => 'text',
        'field_text' => [
          'value' => $blog->field_body_value,
          'format' => 'html',
        ],
        'field_space_above' => 'medium',
        'field_space_below' => 'medium',
      ]);
      $paragraph->save();
      $node = Node::create([
        'type' => 'blog',
        'title' => $blog->title,
        'field_image' => $field_image,
        'field_date' => explode(' ', $blog->field_date_value)[0],
        'field_author_plain' => $blog->field_post_value,
        'field_department' => $field_department,
        'field_components' => [[
          'target_id' => $paragraph->id(),
          'target_revision_id' => $paragraph->getRevisionId(),
        ]],
        'created' => $blog->created,
        'uid' => 1,
        'path' => [
          'alias' => '/' . $blog->alias,
          'pathauto' => PathautoState::SKIP,
        ],
        'status' => $blog->status,
      ]);
      $node->save();
      PathAlias::create([
        'path' => '/node/' . $node->id(),
        'alias' => '/' . $blog->alias,
      ])->save();
    }
  }
}
