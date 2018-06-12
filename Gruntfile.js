module.exports = function (grunt) {

  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),
    pot: {
      options: {
        text_domain: 'woocommerce-4all', //Your text domain. Produces my-text-domain.pot
        dest: 'languages/', //directory to place the pot file
        keywords: ['xgettext', '__'], //functions to look for
        encoding: 'UTF-8'
      },
      files: {
        src: ['**/*.php'], //Parse all php files
        expand: true,
      }
    }
  });

  grunt.loadNpmTasks('grunt-pot');

  grunt.registerTask('default', ['pot']);
};