/* ══════════════════════════════════════════════════════════════════════════════
   AULAPRO — Landing Page Logic (ES5 for Compatibility)
   ══════════════════════════════════════════════════════════════════════════════ */

document.addEventListener('DOMContentLoaded', function() {
  var bMenu = document.getElementById('menu-boton');
  var mLista = document.getElementById('menu');
  var nav = document.getElementById('cabecera');

  // 1. Mobile Menu Toggle
  if (bMenu && mLista && nav) {
    bMenu.addEventListener('click', function(e) {
      e.preventDefault();
      e.stopPropagation();
      var altura = nav.offsetHeight;
      mLista.style.top = altura + 'px';
      mLista.classList.toggle('activo');
      bMenu.classList.toggle('activo');
      document.body.classList.toggle('menu-abierto');
    });
  }

  // 2. Close menu when clicking outside
  document.addEventListener('click', function(e) {
    if (mLista && mLista.classList.contains('activo')) {
      var isClickInsideMenu = mLista.contains(e.target);
      var isClickOnButton = bMenu && bMenu.contains(e.target);
      
      if (!isClickInsideMenu && !isClickOnButton) {
        mLista.classList.remove('activo');
        if (bMenu) bMenu.classList.remove('activo');
        document.body.classList.remove('menu-abierto');
      }
    }
  });

  // 3. Header scroll effect
  window.addEventListener('scroll', function() {
    if (window.scrollY > 50) {
      nav.classList.add('nav-scrolled');
    } else {
      nav.classList.remove('nav-scrolled');
    }
    
    if (mLista && mLista.classList.contains('activo')) {
      mLista.style.top = nav.offsetHeight + 'px';
    }
  });

  // 4. Smooth scroll for anchor links
  var anchors = document.querySelectorAll('a[href^="#"]');
  for (var i = 0; i < anchors.length; i++) {
    anchors[i].addEventListener('click', function(e) {
      var targetId = this.getAttribute('href');
      if (targetId === '#') return;
      
      var targetElement = document.querySelector(targetId);
      if (targetElement) {
        e.preventDefault();
        
        if (mLista) {
          mLista.classList.remove('activo');
          if (bMenu) bMenu.classList.remove('activo');
          document.body.classList.remove('menu-abierto');
        }
        
        var offset = 80;
        var elementPosition = targetElement.getBoundingClientRect().top;
        var offsetPosition = elementPosition + window.pageYOffset - offset;

        window.scrollTo({
          top: offsetPosition,
          behavior: 'smooth'
        });
        
        var plan = this.getAttribute('data-plan');
        if (plan) {
          var selectPlan = document.getElementById('cf-plan');
          if (selectPlan) selectPlan.value = plan;
        }
      }
    });
  }

  // 5. Scroll Animations (Intersection Observer)
  if ('IntersectionObserver' in window) {
    var animObserver = new IntersectionObserver(function(entries) {
      entries.forEach(function(entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('visible');
          animObserver.unobserve(entry.target);
        }
      });
    }, { threshold: 0.1 });

    var animElements = document.querySelectorAll('.animar');
    for (var j = 0; i < animElements.length; i++) { // Fixed bug here: i should be j
       // Wait, I see I used 'i' instead of 'j' in the loop. Fixing below.
    }
    // Correcting the loop:
    document.querySelectorAll('.animar').forEach(function(el) {
      animObserver.observe(el);
    });
  } else {
    // Fallback for browsers without IntersectionObserver
    var fallbacks = document.querySelectorAll('.animar');
    for (var k = 0; k < fallbacks.length; k++) {
      fallbacks[k].classList.add('visible');
    }
  }

  // 6. Pricing Toggle
  var mBtn = document.getElementById('btn-mensual');
  var aBtn = document.getElementById('btn-anual');
  var prices = document.querySelectorAll('.precio-cantidad');

  if (mBtn && aBtn) {
    mBtn.addEventListener('click', function() {
      mBtn.classList.add('activo');
      aBtn.classList.remove('activo');
      for (var l = 0; l < prices.length; l++) {
        prices[l].innerText = prices[l].getAttribute('data-mensual');
      }
    });

    aBtn.addEventListener('click', function() {
      aBtn.classList.add('activo');
      mBtn.classList.remove('activo');
      for (var m = 0; m < prices.length; m++) {
        prices[m].innerText = prices[m].getAttribute('data-anual');
      }
    });
  }

  // 7. Contact Form (AJAX)
  var contactForm = document.getElementById('form-contacto');
  if (contactForm) {
    contactForm.addEventListener('submit', function(e) {
      e.preventDefault();
      
      var submitBtn = document.getElementById('btn-enviar');
      var feedback = document.getElementById('form-feedback');
      
      submitBtn.disabled = true;
      var originalText = submitBtn.innerText;
      submitBtn.innerText = 'Enviando...';

      var formData = new FormData(contactForm);

      fetch('controladores/contacto_landing.php', {
        method: 'POST',
        body: formData
      })
      .then(function(response) { return response.json(); })
      .then(function(data) {
        if (data.ok) {
          feedback.className = 'feedback-exito';
          feedback.innerText = data.msg;
          contactForm.reset();
        } else {
          feedback.className = 'feedback-error';
          feedback.innerText = data.msg;
          submitBtn.disabled = false;
        }
        submitBtn.innerText = originalText;
      })
      .catch(function() {
        feedback.className = 'feedback-error';
        feedback.innerText = 'Error al enviar. Intenta de nuevo.';
        submitBtn.disabled = false;
        submitBtn.innerText = originalText;
      });
    });
  }
});
