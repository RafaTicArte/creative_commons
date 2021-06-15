-- SUMMARY --

The Creative Commons field module defines a field type for attaching Creative
Commons licence types. This allows you to add Creative Commons licences to any 
entity type, including media.

For a full description of the module, visit the project page:
  https://www.drupal.org/project/creative_commons

To submit bug reports and feature suggestions, or to track changes:
  https://www.drupal.org/project/issues/creative_commons


-- REQUIREMENTS --

None.


-- INSTALLATION --

* Install as usual, see http://drupal.org/node/1897420 for further information.


-- CONFIGURATION --

* Configure Creative Commons general settings in Administration » Configuration 
» Content authoring » Creative Commons menu 
  (admin/config/content/creative_commons).

* Add a new field in your entity type.


-- CUSTOMIZATION --

* To override default template copy templates/creative-commons.html.twig file
  in your theme. See inside for an overview of the variables you can use.

* To override field template copy 
  templates/field--node--field-creativecommons.html.twig file in your theme and
  change name with your field name. See inside for an overview of the variables 
  you can use.

-- IMAGES --

* Image files from https://licensebuttons.net/i/.

-- CONTACT --

Current maintainer:
* Rafa Morales (rafaticarte) - https://www.drupal.org/user/1186094
