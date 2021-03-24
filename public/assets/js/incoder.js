
var InCoderAPI = {
    URL: null,
    User: null,
    Pass: null,
    Proxy: function() {
        return 'api/proxy/';
    },
    Params: function(api, params) {

        auth = {};
        auth.api  = InCoderAPI.URL+api;
        auth.user = InCoderAPI.User;
        auth.pass = InCoderAPI.Pass;

        return $.extend(auth, params);
    }
}

var InCoder = {

    ///////////////////////////////////////////////////////////////////////////////////
    // NAVIGATOR //////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////

    Navigator: {

        Loading: {
            Show: function (target) {
                $('#'+target).append($('.navigator-loading').html());
            },
            Hide: function (target) {
                $('#'+target+'>.navigator-loading').remove();
            }
        },

        Folder: {
            Opened: function (target) {
                return $('#'+target+'>ul').is(':visible');
            },
            Toggle: function (target) {
                $('#'+target+'>ul').remove();
            }
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

            if (!InCoderAPI.URL) {

                $('#settings').modal('show');

                return false;
            }

            if ($('#navigator').is(':not(:visible)')) {
                $('.click-toggle-navigator').click();
            }

            InCoder.Navigator.Load('/','navigator');
        },

        Load: function (path, target) {

            if (InCoder.Navigator.Folder.Opened(target)) {
                InCoder.Navigator.Folder.Toggle(target);
                return false;
            }

            InCoder.Navigator.Loading.Show(target);

            var params      = {};
                params.path = path;

            $.post(InCoderAPI.Proxy(), InCoderAPI.Params('navigator/', params), function(response){

                InCoder.Navigator.Loading.Hide(target);

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

        Working: {
            Show: function () {
                $('#editor').append($('.editor-working').html());
            },
            Hide: function () {
                $('#editor>.editor-working').remove();
            }
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

            if ($('#editor').is(':not(:visible)')) {
                $('.click-toggle-navigator').click();
            }

            InCoder.Editor.Working.Show();

            var params      = {};
                params.path = path;

            $.post(InCoderAPI.Proxy(), InCoderAPI.Params('editor/', params), function (response) {

                InCoder.Editor.Working.Hide();

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

                InCoder.Editor.Working.Show();

                var params         = {};
                    params.path    = InCoder.Editor.Path;
                    params.content = InCoder.Editor.Instance.getValue();

                $.post(InCoderAPI.Proxy(), InCoderAPI.Params('editor/', params), function (response) {

                    InCoder.Editor.Working.Hide();

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

        var url  = $('#incoder-api-url').val();
        var user = $('#incoder-api-auth-user').val();
        var pass = $('#incoder-api-auth-pass').val();

        InCoderAPI.URL  = url;
        InCoderAPI.User = user;
        InCoderAPI.Pass = pass;

        InCoder.Navigator.Init();
    });

    $('.click-save-file').click(function(){
        InCoder.Editor.Save();
    });

    $('.click-toggle-navigator').click(function(){

        $('#navigator').toggle();
        $('#editor').toggle();
    });

    /////////////////////////////////////////////////////////////////////////////
    // DEMO SETTINGS ////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////

    $('#settings').on('shown.bs.modal', function () {
        if (!$('#incoder-api-url').val()) {
            $('#incoder-api-url').val('http://www.inicial.com.br/incoder/api/');
            $('#incoder-api-auth-user').val('demo');
            $('#incoder-api-auth-pass').val('demo');
        }
        $('#incoder-api-url').trigger('focus');
    });
});