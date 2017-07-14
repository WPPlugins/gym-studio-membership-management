(function() {
    tinymce.create('tinymce.plugins.fgsmmbtn', {
        /**
         * Initializes the plugin, this will be executed after the plugin has been created.
         * This call is done before the editor instance has finished it's initialization so use the onInit event
         * of the editor instance to intercept that event.
         *
         * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
         * @param {string} url Absolute URL to where the plugin is located.
         */
        init : function(ed, url) {
		    ed.addCommand('fsclassschedulecmd', function() {
				var shortcode = "[fitsoft-code control='class'/]";
				ed.execCommand('mceInsertContent', 0, shortcode);
            });
			
			ed.addCommand('fssignupcmd', function() {
				var shortcode = "[fitsoft-code control='signup'/]";
				ed.execCommand('mceInsertContent', 0, shortcode);
            });
			
			ed.addCommand('fscalendarcmd', function() {
				var shortcode = "[fitsoft-code control='calendar'/]";
				ed.execCommand('mceInsertContent', 0, shortcode);
            });
			
			ed.addCommand('fsmemberlogincmd', function() {
				var shortcode = "[fitsoft-code control='login'/]";
				ed.execCommand('mceInsertContent', 0, shortcode);
            });

			
			ed.addButton('fsclassschedule', {
                title : 'Add Schedule of Classes',
                cmd : 'fsclassschedulecmd',
                image : url + '/editorbtn/fsclassschedule.png'
            });
			
			ed.addButton('fssignup', {
                title : 'Add New Member Signup Form',
                cmd : 'fssignupcmd',
                image : url + '/editorbtn/fssignup.png'
            });
            
            ed.addButton('fscalendar', {
                title : 'Add Monthly Class Calendar',
                cmd : 'fscalendarcmd',
                image : url + '/editorbtn/fscalendar.png'
            });

            ed.addButton('fsmemberlogin', {
                title : 'Add Member\'s Login',
                cmd : 'fsmemberlogincmd',
                image : url + '/editorbtn/fsmemberlogin.png'
            });
        },

        /**
         * Creates control instances based in the incomming name. This method is normally not
         * needed since the addButton method of the tinymce.Editor class is a more easy way of adding buttons
         * but you sometimes need to create more complex controls like listboxes, split buttons etc then this
         * method can be used to create those.
         *
         * @param {String} n Name of the control to create.
         * @param {tinymce.ControlManager} cm Control manager to use inorder to create new control.
         * @return {tinymce.ui.Control} New control instance or null if no control was created.
         */
        createControl : function(n, cm) {
            return null;
        },

        /**
         * Returns information about the plugin as a name/value array.
         * The current keys are longname, author, authorurl, infourl and version.
         *
         * @return {Object} Name/value array containing information about the plugin.
         */
        getInfo : function() {
            return {
                    longname : 'Fitsoft Membership Management Buttons',
                    author : 'Ty.Nguyen',
                    authorurl : 'https://software.fitsoft.com',
                    infourl : 'http://news.fitsoft.com/more-on-wordpress',
                    version : "1.0.2"
            };
        }
    });

    // Register plugin
    tinymce.PluginManager.add('fgsmmbtn', tinymce.plugins.fgsmmbtn);
	
})();