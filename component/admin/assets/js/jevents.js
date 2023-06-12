/**
 * @version    CVS: JEVENTS_VERSION
 * @package    com_jevents
 * @author     Geraint Edwards
 * @copyright  2017--JEVENTS_COPYRIGHT GWESystems Ltd
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 */
// Polyfills for MSIE
if (window.NodeList && !NodeList.prototype.forEach) {
    NodeList.prototype.forEach = Array.prototype.forEach;
}

document.addEventListener('DOMContentLoaded', function() {
    // set container scope for code
    gslUIkit.container = document.getElementById('gslc');

    // reveal right top menu icons
    var navbar = document.querySelector('.gsl-navbar-right  .gsl-navbar-nav');
    if (navbar) {
        navbar.classList.remove('gsl-hidden');
    }

    // fix search button styling
    var buttons = gslUIkit.container.querySelectorAll('.btn-primary');
    buttons.forEach(function(button)
    {
       button.classList.remove('btn-primary');
       button.classList.add('gsl-button-primary');
    });

    buttons = gslUIkit.container.querySelectorAll('.btn');
    buttons.forEach(function(button)
    {
        button.classList.remove('btn');
        button.classList.add('gsl-button');
        //  button.classList.add('gsl-button-small');
        if (!button.classList.contains('gsl-button-primary') && !button.classList.contains('gsl-button-danger') && !button.classList.contains('gsl-button-warning')  && !button.classList.contains('gsl-button-success'))
        {
            button.classList.add('gsl-button-default');
        }
    });
})

ys_popover(".hasYsPopover, .hasPopover");

