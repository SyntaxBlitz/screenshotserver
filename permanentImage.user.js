// ==UserScript==
// @name           Permanent Image
// @author         SyntaxBlitz
// @namespace      http://syntaxblitz.net/
// @description    Lets you turn temporary images into permanent ones.
// @version        1.0
// @include        http://screenshots.your-website-here.com/*

function keyCheck(e) {
	if(e.keyCode == 80) {
		if(id = location.href.match(/screenshots.your-website-here.com\/([0-9a-zA-Z])\.png/i)) {
			location.href = "http://screenshots.your-website-here.com/permanent/" + id[1];
		}
	}
}

window.addEventListener('keydown', keyCheck, true);