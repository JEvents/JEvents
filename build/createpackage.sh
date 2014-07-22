#!/bin/sh
find ./ -type f -name *.zip | xargs rm -f
VERSION=`xml_grep version ../pkg_jevents.xml --text`
VERSION="3.2.0_jq3"
rsync -av --progress ../ ./ --exclude build
zip  -r com_jevents.zip component -x *.svn*
cd plugins
zip  -r --exclude=*.svn* ../finder.zip finder
zip  -r --exclude=*.svn* ../search.zip search
cd ../
cd libraries
zip  -r --exclude=*.svn* ../googl.zip googl
cd ../
cd modules
zip  -r --exclude=*.svn* ../mod_jevents_cal.zip mod_jevents_cal
zip  -r --exclude=*.svn* ../mod_jevents_custom.zip mod_jevents_custom
zip  -r --exclude=*.svn* ../mod_jevents_filter.zip mod_jevents_filter
zip  -r --exclude=*.svn* ../mod_jevents_latest.zip mod_jevents_latest
zip  -r --exclude=*.svn* ../mod_jevents_legend.zip mod_jevents_legend
zip  -r --exclude=*.svn* ../mod_jevents_switchview.zip mod_jevents_switchview
cd ../
zip  -r --exclude=*.svn* "Jevents_$VERSION.zip" install.php pkg_jevents.xml com_jevents.zip finder.zip search.zip googl.zip  mod_jevents_cal.zip mod_jevents_custom.zip mod_jevents_filter.zip   mod_jevents_latest.zip mod_jevents_legend.zip mod_jevents_switchview.zip

find . \! -name "createpackage.sh" \! -name "Jevents_$VERSION.zip" | xargs rm -rf
