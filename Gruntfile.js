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
			},

			uglify: {
				build: {
					files: {
						'build/wp-lazysizes.min.js': ['js/ls.setup.js', 'js/lazysizes/lazysizes.js']
					}
				}
			}
		});

		grunt.loadNpmTasks('grunt-contrib-copy');
		grunt.loadNpmTasks('grunt-contrib-uglify');


		// Default task.
		grunt.registerTask('default', [ 'copy' ]);

		// Concatenate lazysizes JS with mobile detection JS files.
		grunt.registerTask('build', [ 'uglify' ]);
	};
})();
