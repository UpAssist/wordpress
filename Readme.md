# UpAssist/WordPress

1. Inside the themes folder run `composer init themename`
2. `cd themename`
3. `composer require upassist/wordpress@dev`
4. `cp -R vendor/upassist/wordpress/src/theme .`
5. String replace `upassist` (lowercase) to your themename in the copied files.

💥 Make sure you exclude the vendor dir and the composer files in your replace action!
