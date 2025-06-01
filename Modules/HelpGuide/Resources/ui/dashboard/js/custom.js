const sidebarToggle = () => {
    const body = document.body;
    const collapseBtn = document.querySelector(".sidebarToggleBtn");
    const collapseBtnMobile = document.querySelector(".sidebarToggleBtnMobile");
    const collapsedClass = "collapsed";
    headerMobileMenu = document.querySelector(".headerMobileMenu");

    const toggleHeaderMenu = document.querySelectorAll(".HeaderMobileToggleBtn");

    for (const toggleHeaderMenuBtn of toggleHeaderMenu) {
        toggleHeaderMenuBtn.addEventListener("click", function () {
            headerMobileMenu.classList.toggle('is-open');
        });
    }
    
    /*TOGGLE HEADER STATE*/
    collapseBtn.addEventListener("click", function () {

        [].slice.call(document.querySelectorAll('.admin-menu a')).map(function (tooltipTriggerEl) {
            var tti = bootstrap.Tooltip.getInstance(tooltipTriggerEl);
            
            if( document.querySelector('body').classList.contains('collapsed') && tti){
                tti.disable();
            } else {
                tti ? tti.enable() : new bootstrap.Tooltip(tooltipTriggerEl, { placement: 'auto'})
            }
            
        })
       
      body.classList.toggle(collapsedClass);
      this.getAttribute("aria-expanded") == "true"
        ? this.setAttribute("aria-expanded", "false")
        : this.setAttribute("aria-expanded", "true");
      this.getAttribute("aria-label") == "collapse menu"
        ? this.setAttribute("aria-label", "expand menu")
        : this.setAttribute("aria-label", "collapse menu");
    });

    collapseBtnMobile.addEventListener("click", function () {
        body.classList.toggle(collapsedClass);
        this.getAttribute("aria-expanded") == "true"
          ? this.setAttribute("aria-expanded", "false")
          : this.setAttribute("aria-expanded", "true");
        this.getAttribute("aria-label") == "collapse menu"
          ? this.setAttribute("aria-label", "expand menu")
          : this.setAttribute("aria-label", "collapse menu");
    });

    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })

}

window.addEventListener('load', function () {
    sidebarToggle()
})
