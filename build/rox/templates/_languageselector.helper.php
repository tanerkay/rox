<?php

function _languageSelector()
{
    $model = new FlaglistModel();
    $languages = $model->getLanguages();
    $langsel = '';
    $request_string = implode('/',PVars::get()->request);

    foreach($languages as $language) {
        $abbr = $language->ShortCode;
        $title = $language->Name;
        $png = $abbr.'.png';
        if (!isset($_SESSION['lang'])) {
            // hmm
        } else { // if ($_SESSION['lang'] == $abbr) {
            $langsel .=
                "<a href=\"".PVars::getObj("env")->baseuri."rox/in/".$abbr.'/'.$request_string.
                "\">"
                . $title . "</a>\n"
                ;
        }
    }

    return $langsel;
}

function _languageSelectorDropDown()
{
    $words = new MOD_words();
    $model = new FlaglistModel();
    $languages = $model->getLanguages();
    $langsel = '';
    $request_string = implode('/',PVars::get()->request);
    $langsel = '
    <form id="language_select" action="a" method="post">
    '.$words->get('Languages').':
      <select id="language" name="language" class="combo" onchange="window.location.href=this.value; return false">'
    ;
    foreach($languages as $language) {
        $abbr = $language->ShortCode;
        $title = $language->Name;
        $png = $abbr.'.png';
        if (!isset($_SESSION['lang'])) {
            // hmm
        } else {
            $langsel .=
                '<option value="rox/in/'.$abbr.'/'.$request_string.'" '.(($_SESSION['lang'] == $abbr) ? 'selected="selected"' : '') .'>' . $title . '</option>'
                ;
        }
    }
    $langsel .= '
        </select>
    </form>
    ';
    $link = PVars::getObj("env")->baseuri."rox/in/".$abbr.'/'.$request_string;
    return $langsel;
}

?>
