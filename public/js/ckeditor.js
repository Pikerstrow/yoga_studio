/*CK EDITOR*/

import ClassicEditorBase from '@ckeditor/ckeditor5-editor-classic/src/classiceditor';

import EssentialsPlugin from '@ckeditor/ckeditor5-essentials/src/essentials';
import UploadAdapterPlugin from '@ckeditor/ckeditor5-adapter-ckfinder/src/uploadadapter';
import AutoformatPlugin from '@ckeditor/ckeditor5-autoformat/src/autoformat';
import BoldPlugin from '@ckeditor/ckeditor5-basic-styles/src/bold';
import ItalicPlugin from '@ckeditor/ckeditor5-basic-styles/src/italic';
import BlockQuotePlugin from '@ckeditor/ckeditor5-block-quote/src/blockquote';
import EasyImagePlugin from '@ckeditor/ckeditor5-easy-image/src/easyimage';
import HeadingPlugin from '@ckeditor/ckeditor5-heading/src/heading';
import ImagePlugin from '@ckeditor/ckeditor5-image/src/image';
import ImageCaptionPlugin from '@ckeditor/ckeditor5-image/src/imagecaption';
import ImageStylePlugin from '@ckeditor/ckeditor5-image/src/imagestyle';
import ImageToolbarPlugin from '@ckeditor/ckeditor5-image/src/imagetoolbar';
import ImageUploadPlugin from '@ckeditor/ckeditor5-image/src/imageupload';
import LinkPlugin from '@ckeditor/ckeditor5-link/src/link';
import ListPlugin from '@ckeditor/ckeditor5-list/src/list';
import ParagraphPlugin from '@ckeditor/ckeditor5-paragraph/src/paragraph';
import Table from '@ckeditor/ckeditor5-table/src/table';
import TableToolbar from '@ckeditor/ckeditor5-table/src/tabletoolbar';

import Alignment from '@ckeditor/ckeditor5-alignment/src/alignment';
import UploadAdapter from './upload-adapter';

export default class ClassicEditor extends ClassicEditorBase {}

ClassicEditor.builtinPlugins = [
    EssentialsPlugin,
    UploadAdapterPlugin,
    AutoformatPlugin,
    BoldPlugin,
    ItalicPlugin,
    BlockQuotePlugin,
    EasyImagePlugin,
    HeadingPlugin,
    ImagePlugin,
    ImageCaptionPlugin,
    ImageStylePlugin,
    ImageToolbarPlugin,
    ImageUploadPlugin,
    LinkPlugin,
    ListPlugin,
    ParagraphPlugin,
    Alignment,
    Table,
    TableToolbar,
];

ClassicEditor.defaultConfig = {
    toolbar: {
        items: [
            'heading',
            'alignment',
            '|',
            'bold',
            'italic',
            'link',
            'bulletedList',
            'numberedList',
            'imageUpload',
            'blockQuote',
            '|',
            'insertTable',
            '|',
            'undo',
            'redo'
        ]
    },
    alignment: {
        options: [
            'left',
            'right',
            'center',
            'justify'
        ]
    },
    image: {
        toolbar: [
            'imageStyle:full',
            'imageStyle:alignLeft',
            'imageStyle:alignRight',
            '|',
            'imageTextAlternative'
        ],
        styles: [
            // This option is equal to a situation where no style is applied.
            'full',

            // This represents an image aligned to the left.
            'alignLeft',

            // This represents an image aligned to the right.
            'alignRight'
        ]
    },
    table: {
        toolbar: [
            'tableColumn',
            'tableRow',
            '|',
            'mergeTableCells'
        ]
    },
    language: 'ru'
};

import ClassicEditor from './ckeditor';

ClassicEditor
    .create(document.querySelector( '#js-editor' ))
    .then( editor => {
        editor.plugins.get('FileRepository').createUploadAdapter = (loader) => new UploadAdapter(loader);
    } )
    .catch( error => {
        console.error( error.stack );
    });




