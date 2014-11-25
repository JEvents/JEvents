#!/bin/sh
find ./ -type f -name *.zip | xargs rm -f
VERSION=`xml_grep version ../pkg_jevents.xml --text`

rsync -av --progress ../ ./ --exclude build --exclude JEvents --exclude oldreleases
zip  -r com_jevents.zip component -x *.svn*
cd plugins
zip  -r --exclude=*.svn* ../finder.zip finder
zip  -r --exclude=*.svn* ../search.zip search
zip  -r --exclude=*.svn* ../jevents.zip jevents

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

zip  -r --exclude=*.svn* "jevents_$VERSION.zip" install.php pkg_jevents.xml com_jevents.zip language finder.zip search.zip googl.zip jevents.zip  mod_jevents_cal.zip mod_jevents_custom.zip mod_jevents_filter.zip   mod_jevents_latest.zip mod_jevents_legend.zip mod_jevents_switchview.zip


pwd

find . \! -name "createpackage.sh" \! -name "jevents_$VERSION.zip" | xargs rm -rf

