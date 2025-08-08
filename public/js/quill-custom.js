const toolbarOptions = [
    [{
        'header': 1
    }, {
        'header': 2
    }],
    [{
        'list': 'ordered'
    }, {
        'list': 'bullet'
    }],
    [{
        'color': []
    }],
    ['bold', 'italic'],
    ['underline'],
    ['clean']
];

function tieneTexto(htmlString) {
    const div = document.createElement('div');
    div.innerHTML = htmlString;
    const texto = div.textContent || div.innerText;
    return texto.trim().length > 0;
}

function destory_quill(selector) {
    if ($(selector)[0]) {
        var content = $(selector).find('.ql-editor').html();
        $(selector).html('');

        $(selector).siblings('.ql-toolbar').remove();
        $(selector + " *[class*='ql-']").removeClass(function (index, css) {
            return (css.match(/(^|\s)ql-\S+/g) || []).join(' ');
        });

        $(selector + "[class*='ql-']").removeClass(function (index, css) {
            return (css.match(/(^|\s)ql-\S+/g) || []).join(' ');
        });
    }
}

let campo = null;
let quill = null;

function initQuillEditor(toolbar = toolbarOptions, placeholder, id_campo_quill, id_campo_hidden) {
    campo = document.getElementById(id_campo_hidden);

    if (quill) {
        destory_quill('.txt-editor-quill');
    }

    quill = new Quill(`#${id_campo_quill}`, {
        placeholder: placeholder,
        theme: 'snow',
        modules: {
            toolbar: toolbar
        }
    });

    quill.on('text-change', function (delta, oldDelta, source) {
        if (!tieneTexto(quill.root.innerHTML)) {
            campo.value = null;
        } else {
            campo.value = quill.root.innerHTML;
        }
    });

    $(document).ready(function () {
        $(`#${id_campo_quill}`).click(function (event) {
            $(this).addClass('active');
            event.stopPropagation();
        });

        $(document).click(function () {
            $(`#${id_campo_quill}`).removeClass('active');
        });
    });
}