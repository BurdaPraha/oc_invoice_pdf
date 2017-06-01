# Invoices to PDF for Opencart 2.x

note: developed on version 2.3.2, other versions can need some edits - **fell free to contribute!**

## Installation

1. Requiring installed [Vqmod](https://github.com/vqmod/vqmod) because VqMod doesn't support installing via composer itself.
2. `composer require burdapraha/oc_invoice_pdf dev-master`
3. Add this code to your composer.json project file:

```
    "scripts": {
        "post-install-cmd": [
            "php -r \"copy('vendor/burdapraha/oc_invoice_pdf/vqmod/xml/invoice_pdf.xml', 'upload/vqmod/xml/invoice_pdf.xml');\""
        ],
        "post-update-cmd": [
            "php -r \"copy('vendor/burdapraha/oc_invoice_pdf/vqmod/xml/invoice_pdf.xml', 'upload/vqmod/xml/invoice_pdf.xml');\""
        ]
    } 
```
4. add constant to your config.php & admin/config.php for storage PDF files: 
For example: `define('INVOICES_DIR', DIR_IMAGE . '/invoices');` and fix this folder for reading from web by .htaccess
5. optionally you can add row to your `.gitignore` file with path to tracy.xml (example: upload/vqmod/xml/invoice_pdf.xml)
5. celebrate!