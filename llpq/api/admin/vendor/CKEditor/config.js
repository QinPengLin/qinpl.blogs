/**
 * @license Copyright (c) 2003-2017, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
	config.extraPlugins += (config.extraPlugins ? ',code' : 'code');
	config.filebrowserUploadUrl="http://www.qinpl.cn/admin/Info/FileUpload";//域名改为自己的域名
};
