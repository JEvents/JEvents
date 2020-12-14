#!/bin/sh
find ./ -type f -name *.zip | xargs rm -f
VERSION=`xml_grep version ../pkg_jevents.xml --text`

rsync -av --progress ../ ./ --exclude build
zip  -r com_jevents.zip component -x *.svn*
cd plugins
zip  -r --exclude=*.svn* --exclude=*.gitignore* ../finder.zip finder
zip  -r --exclude=*.svn* --exclude=*.gitignore* ../search.zip search
zip  -r --exclude=*.svn* --exclude=*.gitignore* ../jevents.zip jevents
zip  -r --exclude=*.svn* --exclude=*.gitignore* ../gwejson.zip gwejson
zip  -r --exclude=*.svn* --exclude=*.gitignore* ../installer.zip installer
zip  -r --exclude=*.svn* --exclude=*.gitignore* ../actionlog_jevents.zip actionlog
cd ../
cd libraries
zip  -r --exclude=*.svn* --exclude=*.gitignore* ../googl.zip googl
zip  -r --exclude=*.svn* --exclude=*.gitignore* ../jevmodal.zip jevmodal
zip  -r --exclude=*.svn* --exclude=*.gitignore* ../jevtypeahead.zip jevtypeahead
cd ../
cd modules
zip  -r --exclude=*.svn* --exclude=*.gitignore* ../mod_jevents_cal.zip mod_jevents_cal
zip  -r --exclude=*.svn* --exclude=*.gitignore* ../mod_jevents_custom.zip mod_jevents_custom
zip  -r --exclude=*.svn* --exclude=*.gitignore* ../mod_jevents_filter.zip mod_jevents_filter
zip  -r --exclude=*.svn* --exclude=*.gitignore* ../mod_jevents_latest.zip mod_jevents_latest
zip  -r --exclude=*.svn* --exclude=*.gitignore* ../mod_jevents_legend.zip mod_jevents_legend
zip  -r --exclude=*.svn* --exclude=*.gitignore* ../mod_jevents_switchview.zip mod_jevents_switchview
cd ../
zip  -r --exclude=*.svn* --exclude=*.gitignore* "jevents35a2_$VERSION.zip" language install.php pkg_jevents.xml com_jevents.zip finder.zip search.zip gwejson.zip installer.zip actionlog_jevents.zip googl.zip jevtypeahead.zip jevmodal.zip jevents.zip  mod_jevents_cal.zip mod_jevents_custom.zip mod_jevents_filter.zip   mod_jevents_latest.zip mod_jevents_legend.zip mod_jevents_switchview.zip

find . \! -name "createpackage.sh" \! -name "jevents35a2_$VERSION.zip" \! -name "build.xml" \! -name "excludedfiles.build" | xargs rm -rf
