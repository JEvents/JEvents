#!/bin/sh
find ./ -type f -name *.zip | xargs rm -f
VERSION=`xml_grep version ../pkg_jevents.xml --text`

rsync -av --progress ../ ./ --exclude build
zip  -r com_jevents.zip component -x *.svn*
cd plugins
zip  -r --exclude=*.svn* ../finder.zip finder
zip  -r --exclude=*.svn* ../search.zip search
zip  -r --exclude=*.svn* ../jevents.zip jevents
zip  -r --exclude=*.svn* ../gwejson.zip gwejson
cd ../
cd libraries
zip  -r --exclude=*.svn* ../googl.zip googl
zip  -r --exclude=*.svn* ../jevmodal.zip jevmodal
zip  -r --exclude=*.svn* ../jevtypeahead.zip jevtypeahead
cd ../
cd modules
zip  -r --exclude=*.svn* ../mod_jevents_cal.zip mod_jevents_cal
zip  -r --exclude=*.svn* ../mod_jevents_custom.zip mod_jevents_custom
zip  -r --exclude=*.svn* ../mod_jevents_filter.zip mod_jevents_filter
zip  -r --exclude=*.svn* ../mod_jevents_latest.zip mod_jevents_latest
zip  -r --exclude=*.svn* ../mod_jevents_legend.zip mod_jevents_legend
zip  -r --exclude=*.svn* ../mod_jevents_switchview.zip mod_jevents_switchview
cd ../
zip  -r --exclude=*.svn* "jevents34_$VERSION.zip" language install.php pkg_jevents.xml com_jevents.zip finder.zip search.zip gwejson.zip googl.zip jevtypeahead.zip jevmodal.zip jevents.zip  mod_jevents_cal.zip mod_jevents_custom.zip mod_jevents_filter.zip   mod_jevents_latest.zip mod_jevents_legend.zip mod_jevents_switchview.zip

find . \! -name "createpackage.sh" \! -name "jevents34_$VERSION.zip" | xargs rm -rf
