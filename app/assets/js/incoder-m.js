
var InCoderAPI = {
    URL: 'http://www.inicial.com.br/incoder/api/'
}

var InCoder = {

    ///////////////////////////////////////////////////////////////////////////////////
    // NAVIGATOR //////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////

    Navigator: {

        Api: function () {
            return InCoderAPI.URL + 'navigator/';
        },

        Item: {
            Id: 0,
            New: function () {
                InCoder.Navigator.Item.Id++;
                return 'navigator-item-'+InCoder.Navigator.Item.Id;
            },
            Icon: function (type) {
                switch (type) {
                    case 'folder':
                        return $('<i class="fa fa-folder-o" />');
                        break;
                    case 'file':
                        return $('<i class="fa fa-file-text-o" />');
                        break;
                }
            }
        },

        Init: function() {
            InCoder.Navigator.Load('/','navigator');
        },

        Load: function (path, target) {

            $('#'+target+'>ul').remove();

            $.get(InCoder.Navigator.Api(), {path:path}, function(response){
                if (response.status) {

                    ul = $('<ul />');

                    $.each(response.data, function(index, item){

                        type = item.type;
                        path = item.path+item.file;
                        targ = InCoder.Navigator.Item.New();

                        li = $('<li />');
                        li.attr('id', targ);
                        li.addClass(type);

                        a = $('<a href="#" />');
                        a.attr('data-path'  , path);
                        a.attr('data-type'  , type);
                        a.attr('data-target', targ);
                        a.text(item.file);
                        a.prepend(InCoder.Navigator.Item.Icon(type));

                        $(li).append(a);

                        InCoder.Navigator.Bind(a);

                        $(ul).append(li);
                    });

                    $('#'+target).append(ul);

                } else {

                    alert(response.message);
                }
            });
        },

        Bind: function (el) {

            switch(el.attr('data-type')) {
                case 'folder':
                    el.click(function(){
                        InCoder.Navigator.Load(el.attr('data-path'), el.attr('data-target'));
                        $('#navigator a').removeClass('active');
                        $(this).addClass('active');
                    });
                    break;
                case 'file':
                    el.click(function(){
                        InCoder.Editor.Load(el.attr('data-path'));
                        $('#navigator a').removeClass('active');
                        $(this).addClass('active');
                    });
                    break;
            }
        }
    },

    ///////////////////////////////////////////////////////////////////////////////////
    // EDITOR /////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////

    Editor: {

        Path: null,
        Instance: false,
        Changed: false,

        Api: function () {
            return InCoderAPI.URL + 'editor/';
        },

        Init: function () {

            InCoder.Editor.Instance = ace.edit('editor');
            InCoder.Editor.Instance.setTheme("ace/theme/twilight");

            // EVENT FOR CHANGE

            InCoder.Editor.Instance.getSession().on('change', function(e) {
                InCoder.Editor.Changed = true;
            });

            // EVENT FOR SAVE

            InCoder.Editor.Instance.commands.addCommand({
                name: 'save',
                bindKey: {win: 'Ctrl-S',  mac: 'Command-S'},
                exec: function(editor) {
                    InCoder.Editor.Save();
                }
            });
        },

        Mode: function (ext) {

            switch(ext) {

                case 'htm':
                case 'html':
                    return 'ace/mode/html';
                    break;

                case 'js':
                    return 'ace/mode/javascript';
                    break;

                case 'css':
                    return 'ace/mode/css';
                    break;

                case 'php':
                    return 'ace/mode/php';
                    break;

                case 'md':
                    return 'ace/mode/markdown';
                    break;

                default:
                    return null;
                    break;
            }
        },

        Load: function (path) {
            $.get(InCoder.Editor.Api(), {path:path}, function (response) {
                if (response.status) {

                    InCoder.Editor.Instance.getSession().setMode(InCoder.Editor.Mode(path.split('.').pop()));
                    InCoder.Editor.Instance.setValue(response.data.content);
                    InCoder.Editor.Instance.navigateFileStart();
                    InCoder.Editor.Instance.focus();
                    InCoder.Editor.Changed = false;
                    InCoder.Editor.Path = path;

                } else {

                    InCoder.Editor.Path = null;

                    alert(response.message);
                }
            });
        },

        Save: function () {
            if (InCoder.Editor.Path) {
                $.post(InCoder.Editor.Api(), {path:InCoder.Editor.Path, content:InCoder.Editor.Instance.getValue()}, function (response) {
                    if (response.status) {

                        console.log('File saved successfully!');

                    } else {

                        alert(response.message);
                    }
                });
            }
        }
    }
}

$(document).ready(function(){

    InCoder.Navigator.Init();
    InCoder.Editor.Init();

    $('.click-save-settings').click(function(){

        var URL = $('#incoder-api-url').val();

        InCoderAPI.URL = URL;
        InCoder.Navigator.Init();
    });

    $('.click-save-file').click(function(){

        InCoder.Editor.Save();
    });

    $('.click-toggle-navigator').click(function(){

        $('#navigator').toggle();
        $('#editor').toggle();
    });

    $(".collapse-side-menu").sideNav({
        menuWidth: 300,
        edge: 'right',
        closeOnClick: true,
        draggable: true,
        onOpen: function(el) { /* Do Stuff */ },
        onClose: function(el) { /* Do Stuff */ },
    });

    $(".collapse-side-navigator").sideNav({
        menuWidth: 300,
        edge: 'left',
        closeOnClick: false,
        draggable: true,
        onOpen: function(el) { /* Do Stuff */ },
        onClose: function(el) { /* Do Stuff */ },
    });
});