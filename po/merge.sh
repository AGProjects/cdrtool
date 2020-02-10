#!/usr/bin/env sh
make cdrtool.pot
msgmerge ro.po cdrtool.pot > ronew.po; mv ronew.po ro.po
msgmerge nl.po cdrtool.pot > nlnew.po; mv nlnew.po nl.po
msgmerge de.po cdrtool.pot > denew.po; mv denew.po de.po
msgmerge es.po cdrtool.pot > esnew.po; mv esnew.po es.po
