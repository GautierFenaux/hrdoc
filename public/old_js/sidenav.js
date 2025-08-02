const menuHasSubMenu = document.querySelectorAll('.menu-item-has-children');
const ulMenu = document.querySelector('div.menu-list');
const sideNav = document.querySelector('.side-nav');
const links = document.querySelectorAll('.side-nav-focus');
const collaboratorLink = document.querySelector('#collaborator');
const managerLink = document.querySelector('#managerNav');
const rhLink = document.querySelector('#rhNav');

const rhPattern = /^https?:\/\/[^\/]+\/.*rh.*/;
const managerPattern = /manager/;

const profilAccessButton = document.querySelector('.sidenav-profil-wrapper > li');
const profilAccess =  document.querySelector('.profil-access');

const addStyle = (link) => {
    link.classList.add('active');
    const slideStyle = link.querySelector('.slide').style ;
    slideStyle.maxHeight = '200px';
    slideStyle.animation = 'none';
    slideStyle.backgroundColor = '#ffffff82';
}

    Array.prototype.slice.call(links).forEach((link) => {

        if (rhPattern.test(window.location.href)) {
            addStyle(rhLink);

        } else if (managerPattern.test(window.location.href) && managerPattern.test(link.getAttribute('href'))) {
            addStyle(managerLink);

        } else if(!managerPattern.test(window.location.href) && !rhPattern.test(window.location.href)) {
            collaboratorLink.classList.add('active');
        }

    })
    
        window.addEventListener('click', (e) => {
            if (profilAccess.classList.contains('active') && e.target != profilAccessButton && e.target != document.querySelector('.introjs-overlay')) {
                profilAccess.classList.remove('active');
            }
        });
        
        profilAccessButton.addEventListener('click', () => {

            if (profilAccess.firstElementChild.firstElementChild.classList.contains('shining')) {
                profilAccess.firstElementChild.firstElementChild.classList.remove('shining');
                profilAccess.firstElementChild.firstElementChild.style.backgroundColor = 'transparent';
            }
    
            profilAccess.firstElementChild.firstElementChild.addEventListener('mouseenter', () => {
                profilAccess.firstElementChild.firstElementChild.style.backgroundColor = '#acd7c3a6'; // Change to original color on hover
            });
            profilAccess.firstElementChild.firstElementChild.addEventListener('mouseleave', () => {
                profilAccess.firstElementChild.firstElementChild.style.backgroundColor = 'transparent'; // Change to transparent on hover
            });
            profilAccess.classList.toggle('active');
        });
    
