<?php
/**
 * Trouver le type MIME d'un fichier.
 *
 * Cette fonction prend en entrée le nom d'un fichier, et renvoie son type MIME.
 *
 * Merci à Gorn qui nous permet de redistribuer ce fichier sous GPL avec le reste du backend.
 * @author Gorn <jaxx@freesurf.fr>
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 */

function mime($filename){
        $type = array_reverse(explode('.', $filename));
        switch(strtolower($type[0])){
                case 'csm':
                case 'cu':
                        $type = 'application/cu-seeme';
                        break;
                case 'tsp':
                        $type = 'application/dsptype';
                        break;
                case 'spl':
                        $type = 'application/futuresplash';
                        break;
                case 'hqx':
                        $type = 'application/mac-binhex40';
                        break;
                case 'mdb':
                        $type = 'application/msaccess';
                        break;
                case 'doc':
                case 'dot':
                        $type = 'application/msword';
                        break;
                case 'bin':
                        $type = 'application/octet-stream';
                        break;
                case 'oda':
                        $type = 'application/oda';
                        break;
                case 'pdf':
                        $type = 'application/pdf';
                        break;
                case 'pgp':
                        $type = 'application/pgp-signature';
                        break;
                case 'ps':
                case 'ai':
                case 'eps':
                        $type = 'application/postscript';
                        break;
                case 'rtf':
                        $type = 'application/rtf';
                        break;
                case 'xls':
                case 'xlb':
                        $type = 'application/vnd.ms-excel';
                        break;
                case 'ppt':
                case 'pps':
                case 'pot':
                        $type = 'application/vnd.ms-powerpoint';
                        break;
                case 'wmlc':
                        $type = 'application/vnd.wap.wmlc';
                        break;
                case 'wmlsc':
                        $type = 'application/vnd.wap.wmlscriptc';
                        break;
                case 'wp5':
                        $type = 'application/wordperfect5.1';
                        break;
                case 'zip':
                        $type = 'application/zip';
                        break;
                case 'wk':
                        $type = 'application/x-123';
                        break;
                case 'bcpio':
                        $type = 'application/x-bcpio';
                        break;
                case 'pgn':
                        $type = 'application/x-chess-pgn';
                        break;
                case 'cpio':
                        $type = 'application/x-cpio';
                        break;
                case 'deb':
                        $type = 'application/x-debian-package';
                        break;
                case 'dcr':
                case 'dir':
                case 'dxr':
                        $type = 'application/x-director';
                        break;
                case 'dms':
                        $type = 'application/x-dms';
                        break;
                case 'dvi':
                        $type = 'application/x-dvi';
                        break;
                case 'pfa':
                case 'pfb':
                case 'gsf':
                case 'pcf':
                case 'pcf.Z':
                        $type = 'application/x-font';
                        break;
                case 'gnumeric':
                        $type = 'application/x-gnumeric';
                        break;
                case 'gtar':
                case 'tgz':
                        $type = 'application/x-gtar';
                        break;
                case 'hdf':
                        $type = 'application/x-hdf';
                        break;
                case 'phtml':
                case 'pht':
                case 'php':
                        $type = 'application/x-httpd-php';
                        break;
                case 'php3':
                        $type = 'application/x-httpd-php3';
                        break;
                case 'phps':
                        $type = 'application/x-httpd-php3-source';
                        break;
                case 'php3p':
                        $type = 'application/x-httpd-php3-preprocessed';
                        break;
                case 'php4':
                        $type = 'application/x-httpd-php4';
                        break;
                case 'ica':
                        $type = 'application/x-ica';
                        break;
                case 'class':
                        $type = 'application/x-java';
                        break;
                case 'js':
                        $type = 'application/x-javascript';
                        break;
                case 'chrt':
                        $type = 'application/x-kchart';
                        break;
                case 'kil':
                case 'kpr':
                case 'kpt':
                        $type = 'application/x-kpresenter';
                        break;
                case 'ksp':
                        $type = 'application/x-kspread';
                        break;
                case 'kwd':
                case 'kwt':
                        $type = 'application/x-kword';
                        break;
                case 'latex':
                        $type = 'application/x-latex';
                        break;
                case 'lha':
                        $type = 'application/x-lha';
                        break;
                case 'lzh':
                        $type = 'application/x-lzh';
                        break;
                case 'lzx':
                        $type = 'application/x-lzx';
                        break;
                case 'frm':
                case 'maker':
                case 'frame':
                case 'fm':
                case 'fb':
                case 'book':
                case 'fbdoc':
                        $type = 'application/x-maker';
                        break;
                case 'mif':
                        $type = 'application/x-mif';
                        break;
                case 'com':
                case 'exe':
                case 'bat':
                case 'dll':
                        $type = 'application/x-msdos-program';
                        break;
                case 'msi':
                        $type = 'application/x-msi';
                        break;
                case 'nc':
                case 'cdf':
                        $type = 'application/x-netcdf';
                        break;
                case 'pac':
                        $type = 'application/x-ns-proxy-autoconfig';
                        break;
                case 'o':
                        $type = 'application/x-object';
                        break;
                case 'ogg':
                        $type = 'application/x-ogg';
                        break;
                case 'oza':
                        $type = 'application/x-oz-application';
                        break;
                case 'pl':
                case 'pm':
                        $type = 'application/x-perl';
                        break;
                case 'rpm':
                        $type = 'application/x-redhat-package-manager';
                        break;
                case 'shar':
                        $type = 'application/x-shar';
                        break;
                case 'swf':
                case 'swfl':
                        $type = 'application/x-shockwave-flash';
                        break;
                case 'sit':
                        $type = 'application/x-stuffit';
                        break;
                case 'sv4cpio':
                        $type = 'application/x-sv4cpio';
                        break;
                case 'sv4crc':
                        $type = 'application/x-sv4crc';
                        break;
                case 'tar':
                        $type = 'application/x-tar';
                        break;
                case 'gf':
                        $type = 'application/x-tex-gf';
                        break;
                case 'pk':
                        $type = 'application/x-tex-pk';
                        break;
                case 'texinfo':
                case 'texi':
                        $type = 'application/x-texinfo';
                        break;
                case '~':
                case '%':
                case 'bak':
                case 'old':
                case 'sik':
                        $type = 'application/x-trash';
                        break;
                case 't':
                case 'tr':
                case 'roff':
                        $type = 'application/x-troff';
                        break;
                case 'man':
                        $type = 'application/x-troff-man';
                        break;
                case 'me':
                        $type = 'application/x-troff-me';
                        break;
                case 'ms':
                        $type = 'application/x-troff-ms';
                        break;
                case 'ustar':
                        $type = 'application/x-ustar';
                        break;
                case 'src':
                        $type = 'application/x-wais-source';
                        break;
                case 'wz':
                        $type = 'application/x-wingz';
                        break;
                case 'au':
                case 'snd':
                        $type = 'audio/basic';
                        break;
                case 'mid':
                case 'midi':
                        $type = 'audio/midi';
                        break;
                case 'mpga':
                case 'mpega':
                case 'mp2':
                case 'mp3':
                        $type = 'audio/mpeg';
                        break;
                case 'm3u':
                        $type = 'audio/mpegurl';
                        break;
                case 'sid':
                        $type = 'audio/prs.sid';
                        break;
                case 'aif':
                case 'aiff':
                case 'aifc':
                        $type = 'audio/x-aiff';
                        break;
                case 'gsm':
                        $type = 'audio/x-gsm';
                        break;
                case 'ra':
                case 'rm':
                case 'ram':
                        $type = 'audio/x-pn-realaudio';
                        break;
                case 'wav':
                        $type = 'audio/x-wav';
                        break;
                case 'bmp':
                        $type = 'image/bitmap';
                        break;
                case 'gif':
                        $type = 'image/gif';
                        break;
                case 'ief':
                        $type = 'image/ief';
                        break;
                case 'jpeg':
                case 'jpg':
                case 'jpe':
                        $type = 'image/jpeg';
                        break;
                case 'pcx':
                        $type = 'image/pcx';
                        break;
                case 'png':
                        $type = 'image/png';
                        break;
                case 'tiff':
                case 'tif':
                        $type = 'image/tiff';
                        break;
                case 'wbmp':
                        $type = 'image/vnd.wap.wbmp';
                        break;
                case 'ras':
                        $type = 'image/x-cmu-raster';
                        break;
                case 'cdr':
                        $type = 'image/x-coreldraw';
                        break;
                case 'pat':
                        $type = 'image/x-coreldrawpattern';
                        break;
                case 'cdt':
                        $type = 'image/x-coreldrawtemplate';
                        break;
                case 'cpt':
                        $type = 'image/x-corelphotopaint';
                        break;
                case 'jng':
                        $type = 'image/x-jng';
                        break;
                case 'pnm':
                        $type = 'image/x-portable-anymap';
                        break;
                case 'pbm':
                        $type = 'image/x-portable-bitmap';
                        break;
                case 'pgm':
                        $type = 'image/x-portable-graymap';
                        break;
                case 'ppm':
                        $type = 'image/x-portable-pixmap';
                        break;
                case 'rgb':
                        $type = 'image/x-rgb';
                        break;
                case 'xbm':
                        $type = 'image/x-xbitmap';
                        break;
                case 'xpm':
                        $type = 'image/x-xpixmap';
                        break;
                case 'xwd':
                        $type = 'image/x-xwindowdump';
                        break;
                case 'csv':
                        $type = 'text/comma-separated-values';
                        break;
                case 'css':
                        $type = 'text/css';
                        break;
                case 'htm':
                case 'html':
                case 'xhtml':
                        $type = 'text/html';
                        break;
                case 'mml':
                        $type = 'text/mathml';
                        break;
                case 'txt':
                case 'text':
                case 'diff':
                        $type = 'text/plain';
                        break;
                case 'rtx':
                        $type = 'text/richtext';
                        break;
                case 'tsv':
                        $type = 'text/tab-separated-values';
                        break;
                case 'wml':
                        $type = 'text/vnd.wap.wml';
                        break;
                case 'wmls':
                        $type = 'text/vnd.wap.wmlscript';
                        break;
                case 'xml':
                        $type = 'text/xml';
                        break;
                case 'h++':
                case 'hpp':
                case 'hxx':
                case 'hh':
                        $type = 'text/x-c++hdr';
                        break;
                case 'c++':
                case 'cpp':
                case 'cxx':
                case 'cc':
                        $type = 'text/x-c++src';
                        break;
                case 'h':
                        $type = 'text/x-chdr';
                        break;
                case 'csh':
                        $type = 'text/x-csh';
                        break;
                case 'c':
                        $type = 'text/x-csrc';
                        break;
                case 'java':
                        $type = 'text/x-java';
                        break;
                case 'moc':
                        $type = 'text/x-moc';
                        break;
                case 'p':
                case 'pas':
                        $type = 'text/x-pascal';
                        break;
                case 'etx':
                        $type = 'text/x-setext';
                        break;
                case 'sh':
                        $type = 'text/x-sh';
                        break;
                case 'tcl':
                case 'tk':
                        $type = 'text/x-tcl';
                        break;
                case 'tex':
                case 'ltx':
                case 'sty':
                case 'cls':
                        $type = 'text/x-tex';
                        break;
                case 'vcs':
                        $type = 'text/x-vcalendar';
                        break;
                case 'vcf':
                        $type = 'text/x-vcard';
                        break;
                case 'dl':
                        $type = 'video/dl';
                        break;
                case 'fli':
                        $type = 'video/fli';
                        break;
                case 'gl':
                        $type = 'video/gl';
                        break;
                case 'mpeg':
                case 'mpg':
                case 'mpe':
                        $type = 'video/mpeg';
                        break;
                case 'qt':
                case 'mov':
                        $type = 'video/quicktime';
                        break;
                case 'mng':
                        $type = 'video/x-mng';
                        break;
                case 'asf':
                case 'asx':
                        $type = 'video/x-ms-asf';
                        break;
                case 'avi':
                        $type = 'video/x-msvideo';
                        break;
                case 'movie':
                        $type = 'video/x-sgi-movie';
                        break;
                case 'vrm':
                case 'vrml':
                case 'wrl':
                        $type = 'x-world/x-vrml';
                        break;
                default:
                        $type = 'application/octect-stream';
        }
        return $type;
}

?>
