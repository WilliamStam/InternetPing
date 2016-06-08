
var jsfile = [
	'vendor/components/jquery/jquery.js',
	'vendor/components/bootstrap/js/bootstrap.js' ,
	'vendor/components/modernizr/modernizr.js',
	'vendor/ivaynberg/select2/dist/js/select2.full.min.js',
	'app/_js/app.js'
];




module.exports = function (grunt) {
	require('time-grunt')(grunt);
	require('jit-grunt')(grunt);
	
	grunt.initConfig({
		
		concat: {
			js: {
				options: {
					separator: ';',
					stripBanners: true,
					sourceMap :true,
					sourceMapName : 'app/javascript.js.map'
				},
				src: jsfile,
				dest: 'app/javascript.js',
				nonull: true
			},
			js_quick: {
				options: {
					separator: ';',
					stripBanners: true
				},
				src: jsfile,
				dest: 'app/javascript.js',
				nonull: true
			}
		},
		clean: {
			map: ["app/javascript.js.map"],
		},
		
		uglify: {
			js: {
				
				files: {
					'app/javascript.js': ['app/javascript.js']
				}
			}
		},
		less: {
			style: {
				files: {
					"app/style.css": "app/_less/style.less",
				}
			}
		},
		cssmin: {
			options: {
				report: "min",
				keepSpecialComments: 0,
				shorthandCompacting: true
			},
			style: {
				files: {
					'app/style.css': 'app/style.css',
				}
			}
		},
		watch: {
			js: {
				files: ['app/**/*.js'],
				tasks: ['concat:js_quick'],
				options: {
					spawn: false,
					livereload: true
				}
			},
			css: {
				files: ['app/**/*.less','app/style.css'],
				tasks: ['watcher_css'],
				options: {
					spawn: false,
					livereload: true
				}
			},
			twig: {
				files: ['app/**/*.twig'],
				options: {
					spawn: false,
					livereload: true
				}
			}
		}
		
	});
	
	
	
	
	
	
	grunt.registerTask('jsmin', ['uglify:js']);
	grunt.registerTask('js', ['concat:js_quick','clean:map']);
	grunt.registerTask('jsmap', ['concat:js']);
	grunt.registerTask('watcher_css', ['newer:less:style']);
	grunt.registerTask('css', ['less:style']);
	grunt.registerTask('build', ['concat:js','less:style', 'uglify:js','cssmin:style','clean:map']);
	grunt.registerTask('default', ['watch']);


};