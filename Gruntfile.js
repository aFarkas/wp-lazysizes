(function () {
	'use strict';

	module.exports = function (grunt) {
		// Project configuration.
		grunt.initConfig({
			copy: {
				update: {
					files: [
						{
							expand: true,
							cwd: 'node_modules/',
							src: ['lazysizes/**/*.js'],
							dest: 'js/'
						}
					]
				}
			}
		});

		grunt.loadNpmTasks('grunt-contrib-copy');


		// Default task.
		grunt.registerTask('default', [ 'copy' ]);
	};
})();
