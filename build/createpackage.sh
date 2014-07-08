#!/bin/sh
find ./ -type f -name *.zip | xargs rm -f
VERSION=`xml_grep version ../pkg_jevents.xml --text`
VERSION="3.2.0_jq2"
rsync -av --progress ../ ./ --exclude build
zip  -r com_jevents.zip component -x *.svn*
zip  -r --exclude=*.svn* finder.zip plugins/finder
zip  -r --exclude=*.svn* search.zip plugins/search
zip  -r --exclude=*.svn* googl.zip libraries/googl
zip  -r --exclude=*.svn* mod_jevents_cal.zip modules/mod_jevents_cal
zip  -r --exclude=*.svn* mod_jevents_custom.zip modules/mod_jevents_custom
zip  -r --exclude=*.svn* mod_jevents_filter.zip modules/mod_jevents_filter
zip  -r --exclude=*.svn* mod_jevents_latest.zip modules/mod_jevents_latest
zip  -r --exclude=*.svn* mod_jevents_legend.zip modules/mod_jevents_legend
zip  -r --exclude=*.svn* mod_jevents_switchview.zip modules/mod_jevents_switchview
zip  -r --exclude=*.svn* "Jevents_$VERSION.zip" install.php pkg_jevevents.xml com_jevents.zip finder.zip search.zip googl.zip  mod_jevents_cal.zip mod_jevents_custom.zip mod_jevents_filter.zip   mod_jevents_latest.zipmod_jevents_legend.zip mod_jevents_switchview.zip

find . \! -name "createpackage.sh" \! -name "Jevents_$VERSION.zip" | xargs rm -rf
