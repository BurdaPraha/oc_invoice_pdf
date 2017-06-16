# Invoices to PDF for [OpenCart 2.x](https://github.com/opencart/opencart)

note: developed on version 2.3.2, other versions can need some edits - **fell free to contribute!**

## Installation

1. Requiring installed [vQmod](https://github.com/vqmod/vqmod) because vQmod doesn't support installing via composer itself.
2. `composer require burdapraha/oc_invoice_pdf`
3. `composer require sasedev/composer-plugin-filecopier` for files manipulating
4. Add this code to your composer.json project file, extra section:

```
    "extra": {
        "filescopier": [
            {
                "source": "vendor/burdapraha/oc_invoice_pdf/upload",
                "destination": "upload",
                "debug": "true"
            }
        ]
    }    
```
    
It will move vQmod xml file to correct folder.

5. add constant to your config.php & admin/config.php for storage PDF files: 
For example: `define('INVOICES_DIR', DIR_IMAGE . '/invoices');` and fix this folder for reading from web by .htaccess
6. optionally you can add row to your `.gitignore` file with path to invoice_pdf.xml (example: upload/vqmod/xml/invoice_pdf.xml)
7. celebrate!

## Credits

- using [dompdf](https://github.com/dompdf/dompdf) to printing
- inspired by [Invoice to PDF](https://www.opencart.com/index.php?route=marketplace/extension/info&extension_id=26964&filter_search=invoice%20pdf&filter_license=0)
- [hawkey](http://www.opencartex.com/) for talking about solution