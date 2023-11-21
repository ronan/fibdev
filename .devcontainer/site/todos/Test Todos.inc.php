<?php

todo('Test creating a child todo', function() {
  todo('Create a leaf todo', function() {});
  todo('Create a leaf todo with an output', function() {
    return("x> Hello, world!");
  });
  todo('Create a leaf todo with an error', function() {
    return("!");
  });
});
