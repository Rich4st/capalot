const body = jQuery("body"),
  ri = {
    init: function () {
      ri.lazyLoading(), ri.backTotop(), ri.offCanvas(), ri.stickyHeader(), ri.stickyBar(), ri.pagination(), ri.owlCarousel(), ri.gallerylight(),
        ri.CodeHighlight(), ri.add_post_views(), ri.social_action(), ri.add_comment(), ri.setIframeHeight(), ri.PostContents(), ri.pay_action(),
        ri.changeOrder(), ri.account_action()
    },
    intervalId: null,
    currentPopup: null,
    ajax: function ({
      data: e,
      before: t = () => { },
      result: n = () => { },
      complete: o = () => { }
    }) {
      jQuery.ajax({
        url: zb.ajax_url,
        data: e,
        type: "post",
        dataType: "json",
        async: !0,
        success: n,
        error: function (e) {
          ri.notice(e.responseText, 500)
        },
        beforeSend: t,
        complete: o
      })
    },
    notice: function (e = "", t = 220, n = 2e3) {
      let o = jQuery(".ri-notice");
      !e && o.length
        ? o.clearQueue().stop().hide()
        : (!o.length && e && (o = jQuery(`<div class="ri-notice" style="min-width: ${t}px"></div>`), body.append(o)), o.clearQueue().stop().hide().html(e).fadeIn().delay(n).fadeOut())
    },
    popup: function (e, t = 240, n = null) {
      const o = jQuery(`<div class="ri-popup"><div class="ri-popup-body" ${t
        ? `style="width:${t}px"`
        : ""}><div class="ri-popup-close"><span class="svg-close"></span></div><div class="ri-popup-content">${e}</div></div></div>`),
        a = (ri.currentPopup && ri.currentPopup.remove(), ri.currentPopup = o, () => {
          body.removeClass("ri-popup-open"), ri.dimmer("close", 100), o.remove()
        });
      e ? (body.removeClass("ri-popup-open").append(o), ri.notice(!1), setTimeout(() => {
        body.addClass("ri-popup-open"), ri.dimmer("open", 100)
      }, 10), o.on("click touchstart", ".ri-popup-close .svg-close, .dimmer", e => {
        e.preventDefault(), (n || a)(), a()
      })) : a()
    },
    dimmer: function (e, t = 300) {
      var n = jQuery(".dimmer");
      switch (e) {
        case "open":
          n.fadeIn(t);
          break;
        case "close":
          n.fadeOut(t)
      }
    },
    backTotop: function () {
      let e;
      const t = jQuery(".back-top");
      t.length && (window.addEventListener("scroll", function () {
        400 <= (e = document.documentElement.scrollTop || window.pageYOffset || document.body.scrollTop) ? t.addClass("back-top-show") : t.removeClass("back-top-show")
      }), t.on("click", function (e) {
        e.preventDefault(), jQuery("html, body").animate({
          scrollTop: 0
        }, "smooth")
      }))
    },
    offCanvas: function () {
      var e = jQuery(".burger"),
        t = jQuery(".canvas-close"),
        n = jQuery(".dimmer"),
        o = {
          label: "",
          prependTo: ".mobile-menu",
          closedSymbol: '<i class="fas fa-angle-down">',
          openedSymbol: '<i class="fas fa-angle-up">'
        },
        a = body.hasClass("uc-page") ? ".uc-menu-warp" : ".main-menu .nav-list";
      jQuery(a).slicknav(o), e.on("click", function () {
        body.toggleClass("canvas-opened"), body.addClass("canvas-visible"), ri.dimmer("open")
      }), t.on("click", function () {
        body.hasClass("canvas-opened") && (body.removeClass("canvas-opened"), ri.dimmer("close"))
      }), n.on("click", function () {
        body.hasClass("canvas-opened") && (body.removeClass("canvas-opened"), ri.dimmer("close"))
      }), jQuery(document).keyup(function (e) {
        27 == e.keyCode && body.hasClass("canvas-opened") && (body.removeClass("canvas-opened"), ri.dimmer("close"))
      })
    },
    stickyHeader: function () {
      const o = jQuery(".site-header");
      if (o.length) {
        const i = o.outerHeight();
        let n = 0;
        document.addEventListener("scroll", function (e) {
          var t = window.pageYOffset || document.documentElement.scrollTop;
          t > i ? (o.addClass("navbar-now"), t < n && (o.addClass("navbar-sticky"), o.removeClass("navbar-now"))) : (o.removeClass("navbar-now"), 0 === t && o.removeClass("navbar-sticky")), n = t
        })
      }
      var e = jQuery(".site-header .nav-list"),
        t = window.location.href;
      e.find("li").removeClass("current-menu-item"), e.find(`a[href="${t}"]`).closest("li").addClass("current-menu-item");
      jQuery(".toggle-notify").on("click", function (e) {
        e.preventDefault();
        const t = jQuery(this).find("i"),
          n = t.attr("class");
        ri.ajax({
          data: {
            action: "zb_get_site_notify",
            nonce: zb.ajax_nonce
          },
          before: () => {
            t.removeClass().addClass("fas fa-fan fa-spin")
          },
          result: ({
            status: e,
            msg: t
          }) => {
            1 == e ? ri.popup(t, 380) : ri.notice(t)
          },
          complete: () => {
            t.removeClass().addClass(n)
          }
        })
      });
      e = jQuery(".toggle-search");
      const n = jQuery(".navbar-search");
      e.on("click", function (e) {
        e.stopPropagation(), n.toggleClass("show")
      }), n.on("click", function (e) {
        e.stopPropagation()
      }), jQuery(document).click(function () {
        n.removeClass("show")
      });
      t = jQuery(".toggle-color");
      const a = window.location.hostname;
      t.click(function () {
        var e = jQuery(this).find(".show"),
          t = e.next(".toggle-color>span");
        0 === t.length && (t = jQuery(".toggle-color>span:first-child")), e.removeClass("show"), t.addClass("show"), e = t.data("mod"), jQuery("html").attr("data-bs-theme", e), jQuery.cookie("_zb_current_site_color", e, {
          domain: a,
          path: "/"
        })
      })
    },
    stickyBar: function () {
      var e = jQuery("[data-sticky]"),
        t = e.siblings("[data-sticky-content]");
      e.height() < t.height() && e.length && e.theiaStickySidebar({
        updateSidebarHeight: !1,
        additionalMarginTop: 30
      })
    },
    lazyLoading: function () {
      0 < jQuery(".lazy").length && (window.lazyLoadInstance = new LazyLoad({}))
    },
    setIframeHeight: function () {
      var e = jQuery(".post-content");
      const n = e.width();
      Array.from(e.find("iframe")).forEach(function (e) {
        var t = 9 * n / 16;
        jQuery(e).css({
          height: t,
          width: "100%"
        })
      })
    },
    heroVideoJs: function (t) {
      var e = document.querySelector(".video-js");
      const n = videojs(e),
        o = (n.on("contextmenu", function (e) {
          e.preventDefault()
        }), n.ready(i), jQuery(".switch-video")),
        a = jQuery(".video-title .title-span");

      function i() {
        var e = n.currentType();
        /^audio/.test(e) ? ((e = n.el().querySelector(".centered-html-cd")) || n.el().insertAdjacentHTML("beforeend", '<div class="centered-html-cd"><div class="souse-img"><div class="icon-cd"></div><div class="icon-left"></div></div>'), (e = n.el().querySelector(".centered-html-cd")) && e.addEventListener("click", function () {
          n.paused() ? n.play() : n.pause()
        }), n.on(["playing", "pause"], function () {
          var e = n.el().querySelector(".icon-cd"),
            t = n.el().querySelector(".icon-left");
          e.classList.toggle("rotate", !n.paused()), t.classList.toggle("skewing", !n.paused())
        })) : ((e = n.el().querySelector(".centered-html-cd")) && e.parentNode.removeChild(e), n.off(["playing", "pause"]))
      }
      o.on("click", function () {
        var e;
        jQuery(this).hasClass("active") || (e = jQuery(this).data("index"), e = t[e], a.text(e.title), n.poster(e.img), e.src && (n.src({
          src: e.src,
          type: e.type
        }), n.play()), o.removeClass("active"), jQuery(this).addClass("active"), n.off("ready", i), n.ready(i))
      })
    },
    PostContents: function () {
      var e = jQuery(".post-buy-widget"),
        t = e.find(".ri-down-warp").data("resize"),
        n = jQuery(".post-content");
      new ClipboardJS(".copy-pwd", {
        text: function (e) {
          return e.getAttribute("data-pwd")
        }
      }).on("success", function (e) {
        ri.notice(zb.gettext.__copypwd)
      }), jQuery(window).width() < 992 && ("top" === t ? e.prependTo(n) : e.appendTo(n))
    },
    CodeHighlight: function () {
      if (0 != zb.singular_id) {
        var e = jQuery(".post-content"),
          t = jQuery(".sidebar"),
          e = e.find("h1, h2, h3");
        if (1 == zb.post_content_nav && 0 < e.length) {
          e = e.map(function (e) {
            var t = jQuery(this);
            let n = t.attr("id");
            return n || (n = "header-" + e, t.attr("id", n)), {
              level: parseInt(t.prop("tagName").substring(1)),
              id: n,
              text: t.text()
            }
          }).get();
          const a = jQuery('<ul class="h-navList">');
          jQuery.each(e, function (e, t) {
            var n = jQuery("<li>"),
              o = jQuery("<a>").attr("href", "#" + t.id).text(t.text);
            switch (t.level) {
              case 1:
                n.addClass("nav-h1");
                break;
              case 2:
                n.addClass("nav-h2");
                break;
              case 3:
                n.addClass("nav-h3")
            }
            n.append(o), a.append(n)
          }), t.append(a)
        }
        document.querySelectorAll(".post-content pre").forEach(e => {
          e.classList.contains("highlighted") || (e.classList.add("highlighted"), e.innerHTML = e.innerHTML.trim(), hljs.highlightElement(e))
        })
      }
    },
    owlCarousel: function () {
      const t = {
        autoplay: !1,
        loop: !1,
        items: 1,
        margin: 10,
        lazyLoad: !1,
        nav: !0,
        dots: !0,
        navText: ['<i class="fa fa-angle-left"></i>', '<i class="fa fa-angle-right"></i>'],
        navElement: "div"
      };
      var e = jQuery(".owl-carousel");
      e.length && e.each(function () {
        var e = jQuery(this).data("config") || {},
          e = $.extend({}, t, e);
        jQuery(this).owlCarousel(e)
      })
    },
    pagination: function () {
      var e = jQuery(".posts-warp");
      const o = jQuery(".infinite-scroll-button"),
        a = jQuery(".infinite-scroll-status"),
        i = jQuery(".infinite-scroll-msg");
      var t = o.hasClass("infinite-click"),
        n = (o.hasClass("infinite-auto"), {
          append: ".posts-warp > *",
          debug: !1,
          hideNav: ".pagination",
          history: !1,
          path: ".pagination a.page-next",
          prefill: !0
        });
      o.length && (t && (n.button = ".infinite-scroll-button", n.prefill = !1, n.scrollThreshold = !1), e.on("request.infiniteScroll", function (e, t) {
        a.show()
      }), e.on("load.infiniteScroll", function (e, t, n) {
        a.hide()
      }), e.on("append.infiniteScroll", function (e, t, n, o, a) {
        window.lazyLoadInstance.update()
      }), e.on("last.infiniteScroll", function (e, t, n) {
        o.hide(), i.show()
      }), e.infiniteScroll(n))
    },
    add_post_views: function () {
      0 != zb.singular_id && jQuery.post(zb.ajax_url, {
        action: "zb_add_post_views",
        nonce: zb.ajax_nonce,
        post_id: zb.singular_id
      })
    },
    gallerylight: function () {
      var e = jQuery(".post-content");
      e.length && (e = e.find('a[href$=".jpg"], a[href$=".jpeg"], a[href$=".png"], a[href$=".gif"]')).length && e.click(function (e) {
        e.preventDefault();
        var t, e = jQuery(this).closest(".gallery");
        e.length ? (t = (e = e.find(".gallery-item")).index(jQuery(this).closest(".gallery-item")) + 1, e = e.map(function () {
          return {
            title: jQuery(this).find(".gallery-caption").text(),
            src: jQuery(this).find("a").attr("href")
          }
        }).get(), Spotlight.show(e, {
          index: t
        })) : Spotlight.show(jQuery(this))
      })
    },
    social_action: function () {
      const n = jQuery(".post-fav-btn"),
        o = jQuery(".post-like-btn"),
        a = jQuery(".post-share-btn"),
        i = "post_like_storage";
      new ClipboardJS(".user-select-all", {
        text: function (e) {
          return e.textContent
        }
      }).on("success", function (e) {
        ri.notice(zb.gettext.__copy_succes)
      }), o.on("click", function () {
        if (localStorage.getItem(i) == zb.singular_id) return o.addClass("disabled"), ri.notice(o.data("text")), !1;
        const e = jQuery(this).find("i"),
          t = e.attr("class");
        ri.ajax({
          data: {
            action: "zb_add_like_post",
            nonce: zb.ajax_nonce,
            post_id: zb.singular_id
          },
          before: () => {
            o.addClass("disabled"), e.removeClass().addClass("fa fa-spinner fa-spin me-1")
          },
          result: ({
            status: e,
            msg: t
          }) => {
            1 == e ? localStorage.setItem(i, zb.singular_id) : (o.removeClass("disabled"), localStorage.removeItem(i)), ri.notice(t)
          },
          complete: () => {
            e.removeClass().addClass(t)
          }
        })
      }), n.on("click", function () {
        const e = jQuery(this).find("i"),
          t = e.attr("class");
        ri.ajax({
          data: {
            action: "zb_add_fav_post",
            nonce: zb.ajax_nonce,
            is_add: n.data("is"),
            post_id: zb.singular_id
          },
          before: () => {
            n.addClass("disabled"), e.removeClass().addClass("fa fa-spinner fa-spin me-1")
          },
          result: ({
            msg: e
          }) => {
            ri.notice(e)
          },
          complete: () => {
            e.removeClass().addClass(t)
          }
        })
      }), a.on("click", function () {
        const e = jQuery(this).find("i"),
          t = e.attr("class");
        ri.ajax({
          data: {
            action: "zb_add_share_post",
            nonce: zb.ajax_nonce,
            post_id: zb.singular_id
          },
          before: () => {
            a.addClass("disabled"), e.removeClass().addClass("fa fa-spinner fa-spin me-1")
          },
          result: ({
            msg: e
          }) => {
            ri.drawSharePoster(e.data, e.html)
          },
          complete: () => {
            a.removeClass("disabled"), e.removeClass().addClass(t)
          }
        })
      })
    },
    drawSharePoster: function (r, s) {
      const l = document.createElement("canvas"),
        c = l.getContext("2d"),
        e = 640,
        d = 180,
        t = "bold 24px Arial",
        u = "18px Arial",
        n = f(r.title, 600, 2, t),
        o = f(r.desc, 600, 3, u),
        p = 580 + 32 * (n.length + o.length + 1) + 80 + 14,
        a = (l.width = e, l.height = p, c.fillStyle = "#FFFFFF", c.fillRect(0, 0, e, p), new Image);

      function f(e, t, n, o) {
        var a = document.createElement("canvas").getContext("2d"),
          i = [];
        let r = "";
        a.font = o;
        var s = jQuery("<div>").html(e).text().split("");
        for (let e = 0; e < s.length; e++) {
          var l = s[e];
          if (a.measureText(r + l).width < t && (!n || i.length < n)) r, r += l;
          else if (i.push(r), r = l, n && i.length === n) break
        }
        return i.push(r), i
      }

      function m(t, n, o, a, i) {
        for (let e = 0; e < t.length; e++) {
          var r = t[e];
          i.fillText(r, n, o + e * a)
        }
      }
      a.src = r.img, a.crossOrigin = "anonymous", a.onerror = () => (console.log("thumbnailImg error"), ri.popup(s), !1), a.onload = () => {
        c.drawImage(a, 20, 20, 600, 400);
        c.font = "bold 100px Arial", c.fillStyle = "#f1f1f1", c.fillText(r.date_day, 40, 378), c.font = "bold 22px Arial", c.fillStyle = "#f1f1f1";
        var e = c.measureText(r.date_year).width,
          e = (c.fillText(r.date_year, 40 + (100 - e) / 2, 400), c.font = t, c.fillStyle = "#494949", m(n, 20, 472, 32, c), c.font = "14px Arial", c.fillStyle = "#009688", 472 + 32 * (n.length + 1) - 32),
          e = (c.fillText(r.category, 20, e), c.font = u, c.fillStyle = "#646977", 32 + e);
        m(o, 20, e, 25.6, c);
        const i = new Image;
        i.src = r.site_logo, i.crossOrigin = "anonymous", i.onerror = () => (console.log("logoImg error"), ri.popup(s), !1), i.onload = () => {
          var e = Math.min(80 / i.height, 600 / i.width),
            t = i.width * e,
            e = i.height * e,
            n = p - 20 - d,
            t = (c.drawImage(i, 20, n, t, e), c.font = "bold 18px Arial", c.fillStyle = "#494949", n + e + 20),
            n = (c.fillText(r.site_name, 20, t), c.font = "15px Arial", c.fillStyle = "#646977", 32 + t);
          m(f(r.site_desc, 400, 2, u), 20, n, 25.6, c);
          const o = p - 20 - d,
            a = new Image;
          a.src = r.qrcode, a.crossOrigin = "anonymous", a.onerror = () => (console.log("qrcodeImg error"), ri.popup(s), !1), a.onload = () => {
            c.drawImage(a, 440, o, d, d), c.lineWidth = 2, c.strokeStyle = "#dddddd", c.strokeRect(440, o, d, d);
            c.measureText(r.url).width;
            var e = p - 20,
              e = (c.font = "13px Arial", c.fillStyle = "#dddddd", c.fillText(r.url, 20, e), l.toDataURL("image/png")),
              t = jQuery(s);
            t.find(".share-qrcode").attr("src", e).addClass("p-0"), ri.popup(t.prop("outerHTML"), 320), jQuery(".share-qrcode").click(function () {
              var e = jQuery(this).attr("src"),
                t = jQuery("<a>").hide(),
                n = r.title;
              t.attr("href", e), t.attr("download", n), jQuery("body").append(t), t[0].click(), t.remove()
            })
          }
        }
      }
    },
    add_comment: function () {
      const o = jQuery("#commentform");
      o.find('input[type="submit"]'), o.submit(function (e) {
        e.preventDefault();
        const t = jQuery("#submit"),
          n = t.val();
        jQuery.ajax({
          type: "POST",
          url: zb.ajax_url,
          data: o.serialize() + "&action=zb_ajax_comment&nonce=" + zb.ajax_nonce,
          beforeSend: function (e) {
            t.prop("disabled", !0).val(zb.gettext.__comment_be)
          },
          error: function (e, t, n) {
            ri.notice(e.responseText)
          },
          success: function (e) {
            "success" == e ? (t.val(zb.gettext.__comment_succes), ri.notice(zb.gettext.__comment_succes_n), setTimeout(function () {
              window.location.reload()
            }, 2e3)) : ri.notice(e)
          },
          complete: function (e) {
            t.prop("disabled", !1).val(n)
          }
        })
      });
      var e = jQuery(".comments-list");
      const a = jQuery(".infinite-scroll-button"),
        i = jQuery(".infinite-scroll-status"),
        r = jQuery(".infinite-scroll-msg");
      a.length && (e.on("request.infiniteScroll", function (e, t) {
        i.show()
      }), e.on("load.infiniteScroll", function (e, t, n) {
        i.hide()
      }), e.on("last.infiniteScroll", function (e, t, n) {
        a.hide(), r.show()
      }), e.infiniteScroll({
        append: ".comments-list > *",
        debug: !1,
        hideNav: ".comments-pagination",
        history: !1,
        path: ".comments-pagination a.next",
        prefill: !1,
        scrollThreshold: !1,
        button: ".infinite-scroll-button"
      }))
    },
    post_tougao: function () {
      const o = jQuery(".tougao_thumbnail");
      o.on("click", function () {
        const n = wp.media({
          multiple: !1
        });
        n.on("select", function () {
          var e = n.state().get("selection").first().toJSON(),
            t = e.url,
            e = e.id;
          jQuery("#_thumbnail_id").val(e), o.empty(), e = jQuery("<img>").attr("src", t), o.append(e)
        }), n.open()
      }), body.on("click", ".add-input-file", function () {
        const t = jQuery(this).closest(".input-group").find(".input-file-url"),
          n = wp.media({
            multiple: !1
          });
        n.on("select", function () {
          var e = n.state().get("selection").first().toJSON().url;
          t.val(e)
        }), n.open()
      });
      const e = jQuery("#cao_video_switch"),
        t = jQuery("#cao_status_switch"),
        n = jQuery("#price-input-warp"),
        a = jQuery("#down-input-warp"),
        i = jQuery("#video-input-warp");

      function r() {
        e.is(":checked") || t.is(":checked") ? n.show() : n.hide(), t.is(":checked") ? a.show() : a.hide(), e.is(":checked") ? i.show() : i.hide()
      }
      r(), e.on("change", r), t.on("change", r), jQuery(".meta-input-item-add").on("click", function () {
        var e = jQuery(this).closest(".meta-input-warp").find(".meta-input-group");
        let t = e.find(".meta-input-item").length;
        var n = e.find(".meta-input-item:first").clone();
        n.find("input").each(function () {
          var e = jQuery(this).attr("name").replace(/\[\d+\]/g, "[" + t + "]");
          jQuery(this).attr("name", e), jQuery(this).val("")
        }), e.append(n)
      }), jQuery(".meta-input-group").on("click", ".meta-input-item-remove", function () {
        var e = jQuery(this).closest(".meta-input-item");
        0 !== e.index() && e.remove()
      })
    },
    account_action: function () {
      const a = jQuery("#captcha-img"),
        e = jQuery("input[name='captcha_code']"),
        t = jQuery("#account-from"),
        i = jQuery("#click-submit");
      a.on("click", function () {
        ri.ajax({
          data: {
            action: "zb_get_captcha_img",
            nonce: zb.ajax_nonce
          },
          before: () => { },
          result: ({
            status: e,
            msg: t
          }) => {
            1 == e ? a.attr("src", t) : ri.notice(t)
          },
          complete: () => {
            e.val("")
          }
        })
      }), i.on("click", function (e) {
        e.preventDefault();
        e = t.serializeArray();
        let o = decodeURIComponent(location.href.split("redirect_to=")[1] || ""),
          n = {
            nonce: zb.ajax_nonce
          };
        e.forEach(({
          name: e,
          value: t
        }) => {
          n[e] = t
        }), ri.ajax({
          data: n,
          before: () => {
            i.prop("disabled", !0)
          },
          result: ({
            status: e,
            msg: t,
            back_url: n
          }) => {
            ri.notice(t), 1 == e && setTimeout(() => {
              (o = window.frames.length !== parent.frames.length ? "" : o) ? window.location.href = o : n ? window.location.href = n : window.location.reload()
            }, 2e3)
          },
          complete: () => {
            a.attr("src", a.attr("data-src")), i.prop("disabled", !1)
          }
        })
      })
    },
    pay_action: function () {
      var e = jQuery(".js-pay-action");
      let o = null;
      e.on("click", function () {
        var e = jQuery(this);
        o = {
          nonce: zb.ajax_nonce,
          post_id: e.data("id"),
          order_type: e.data("type"),
          order_info: e.data("info")
        };
        const t = e.find("i"),
          n = t.attr("class");
        t.length && (t.removeClass().addClass("fa fa-spinner fa-spin me-1"),
          setTimeout(() => {
            t.removeClass().addClass(n)
          }, 700))
        ri.get_pay_select_html(o)
      })
      body.on("click", ".pay-item", function () {
        const e = jQuery(this),
          t = (o.pay_type_id = e.data("id"), o.action = "zb_get_pay_action", e.find("i")),
          n = t.attr("class");

        e.data("click") || ri.ajax({
          data: o,
          before: () => {
            e.data("click", "1"), t.removeClass().addClass("fa fa-spinner fa-spin"), ri.notice(zb.gettext.__buy_be_n)
          },
          result: e => {
            1 == e.status ? (ri.notice(!1), ri.pay_result_callback(e), ri.changeOrder(5e3, !1)) : ri.notice(e.msg)
          },
          complete: () => {
            t.removeClass().addClass(n)
          }
        })
      })
    },
    get_pay_select_html: function (e) {
      e.action = "zb_get_pay_select_html", ri.ajax({
        data: e,
        result: ({
          status: e,
          msg: t
        }) => {
          1 == e ? ri.popup(t, 240) : ri.notice(t)
        }
      })
    },
    order_num_cookie(e = "remove") {
      var t = "_zb_current_order_num",
        n = window.location.host;
      return "remove" === e ? jQuery.removeCookie(t, {
        domain: n,
        path: "/"
      }) : jQuery.cookie(t)
    },
    pay_result_callback: function (e) {
      if (0 == e.status) return ri.notice(e.msg), !1;
      "popup" == e.method ? ri.popup(e.msg, 280, () => {
        ri.notice(zb.gettext.__buy_no_n), clearInterval(ri.intervalId), ri.order_num_cookie("remove")
      }) : "url" == e.method ? window.location.href = e.msg : "reload" == e.method && (ri.notice(e.msg), ri.popup(!1), setTimeout(() => location.reload(), 2e3))
    },
    changeOrder: function (e = 5e3, t = !0) {
      clearInterval(ri.intervalId);
      var n = ri.order_num_cookie("get");
      if (!n) return !1;
      const o = e => {
        ri.ajax({
          data: {
            action: "zb_check_pay_status",
            nonce: zb.ajax_nonce,
            num: e
          },
          result: ({
            status: e,
            msg: t,
            back_url: n
          }) => {
            1 == e && (clearInterval(ri.intervalId), ri.order_num_cookie("remove"), ri.notice(t), ri.popup(!1), setTimeout(() => window.location.href = n, 2e3))
          }
        })
      };
      t && o(n), ri.intervalId = setInterval(() => {
        var e = jQuery.cookie("_zb_current_order_num");
        return e ? (o(e), !0) : (clearInterval(ri.intervalId), !1)
      }, e)
    }
  };
jQuery(function (e) {
  ri.init(), console.group(window.location.host), console.log("\n%c %s %c %s\n", "color: #fff; background: #34495e; padding:5px 0;", "RiPro-V5主题", "background: #fadfa3; padding:5px 0;", "https://ritheme.com"), console.log("%c 性能", "color:orange; font-weight: bold", e("#debug-info").text()), console.groupEnd()
});
