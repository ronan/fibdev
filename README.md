# dropdev

A Dev Container based local dev tool for Drupal9/Drupal10 migrations

```

  ═╦════╗
   ║  [ d ]
___╩___
\      |      [ r ][ o ][ p ]  _________
 \     |_[ - ][ d ][ e ][ v ]_/ o o o /
  \__________________________________/
 ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

```

## TODOs

- [ ] Use mDNS to remove the need for different ports (eg: http://backsdrop.local http://phpmyadmin.local)
- [x] Get phpinfo working
- [x] Automatically restore from backup.
- [x] Get bee (https://backdropcms.org/project/bee) working
- [ ] Proxy files from prod
  - [x] Option 1: Redirect in nginx.conf
    - [ ] Exclude generated css/js
  - [ ] Option 2: Configure base_url in Drupal to point to prod
  - [ ] Option 3: Pull files from prod and cache and serve locally
- [ ] Live previews
- [x] Suppress debugging warning
- [x] Allow for local module install via UI
- [x] Get xdebug working for app container
- [ ] Visual Regression testing
  - [x] Determine urls to test
    - [ ] Pull from top viewed pages if drupal is tracking that?
    - [x] Crawl the site to a few levels deep
  - [x] Run backstop js on new site vs d9 site (or prod)
  - [ ] Fix issue where backstop.js exists during the report creation
  - [ ] Upload or host report for client review
