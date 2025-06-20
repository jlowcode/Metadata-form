/**
 * Metadata JavaScript for Fabrik Form Plugin
 *
 * @copyright: Copyright (C) 2025 Jlowcode Org - All rights reserved.
 * @license  : GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

define(['jquery', 'fab/fabrik'], function (jQuery, Fabrik) {
  'use strict';

  var Metadata = new Class({
    initialize: function () {
      document.addEventListener('DOMContentLoaded', bind(this));
    },
  });

  return new Metadata(); 
});
