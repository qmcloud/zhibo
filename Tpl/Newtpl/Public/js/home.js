(function(t) {
	"use strict";
	if (t.define) {
		return
	}

	function e(t) {
		return Object.prototype.toString.call(t) === "[object Function]"
	}
	var r = {};
	var n = null;
	var i = t.document.getElementsByTagName("script");
	var a;
	for (a = 0; a < i.length && !n; a++) {
		n = i[a].getAttribute("data-main")
	}
	if (!n) {
		throw new Error("No data-main attribute in script tag.")
	}
	var o;
	var f = function u(t) {
		var n = {};
		var i = r[t];
		if (e(r[t].factory)) {
			var a = r[t].factory.apply(undefined, [o, n, i]);
			i.ret = a === undefined ? i.exports : a
		} else {
			i.ret = r[t].factory
		}
		i.inited = true
	};
	o = function c(t) {
		if (!r[t]) {
			throw new Error("Module " + t + " is not defined.")
		}
		var e = r[t];
		if (e.inited === false) {
			f(t)
		}
		return e.ret
	};
	var d = function l(t, i, a) {
		if (r[t]) {
			throw new Error("Module " + t + " has been defined already.")
		}
		if (e(i)) {
			a = i
		}
		r[t] = {
			factory: a,
			inited: false
		};
		if (t === n) {
			f(t)
		}
	};
	t.define = d
})(window);
define("basic/Observer", function(e, t, n) {
	var i = [];
	var r = {
		Publisher: function(e) {
			this.eventName = e
		},
		subscribe: function(e) {
			var t = false;
			for (var n = 0; n < i.length; n++) {
				if (i[n] === e) {
					t = true;
					break
				}
			}
			if (!t) {
				i.push(e)
			}
		},
		unsubscribe: function(e) {
			var t = [];
			for (var n = 0; n < i.length; n++) {
				if (i[n] != e) {
					t.push(i[n])
				}
			}
			i = t
		}
	};
	r.Publisher.prototype.deliver = function() {
		var e = this,
			t = arguments || null;
		for (var n = 0; n < i.length; n++) {
			if (e.eventName == i[n].eventName) {
				i[n].func(t)
			}
		}
	};
	return r
});
define("basic/Util", function(e, n, r) {
	var s = e("basic/jquery");
	var a = {};
	var o = {};
	var l = /[\r\t\n]/g;
	var c = /^\s+/;
	var u = /\s+$/;
	var f = />\s*(.*?)\s*</g;
	var d = /((^|%>)[^\t]*)'/g;
	var p = /\t=(.*?)%>/g;
	var h, m, g, v, y, b;

	function w(e) {
		return e < 10 ? "0" + e : e
	}

	function x(e) {
		switch (e) {
			case "yyyy":
				return h;
			case "yy":
				return h.toString().slice(-2);
			case "MM":
				return w(m);
			case "M":
				return m;
			case "dd":
				return w(g);
			case "d":
				return g;
			case "HH":
				return w(v);
			case "H":
				return v;
			case "hh":
				return w(v > 12 ? v - 12 : v);
			case "h":
				return v > 12 ? v - 12 : v;
			case "mm":
				return w(y);
			case "m":
				return y;
			case "ss":
				return w(b);
			case "s":
				return b;
			default:
				return e
		}
	}

	function T(e) {
		return "var p=[];" + "with(tmplData){p.push('" + e.replace(c, "").replace(u, "").replace(l, " ").replace(f, ">$1<").split("<%").join("	").replace(d, "$1\r").replace(p, "',$1,'").split("	").join("');\n").split("%>").join("\np.push('").split("\r").join("\\'") + "');}return p.join('');"
	}

	function k(e, t) {
		var n = "",
			i = document.getElementById("tmpl_" + e);
		var r = !/\W\//.test(e) && i ? o[e] = o[e] || k(i.innerHTML) : new Function("tmplData", "tmpl", T(e));
		if (t) {
			try {
				return r(t, C)
			} catch (s) {}
		}
		return r
	}

	function C(e, t) {
		t = t || {};
		if (typeof e === "function") {
			return e(t)
		}
		return k(e, t)
	}
	var j = {
		format: function(e) {
			if (typeof e == "undefined") {
				return ""
			}
			if (typeof e != "object") {
				throw new Error("data sended to the server must be 'object'")
			}
			var t = [];
			for (var n in e) {
				t.push(encodeURIComponent(n) + "=" + encodeURIComponent(e[n]))
			}
			return t.join("&").replace(/%20/g, "+")
		},
		log: function(e) {},
		login: function() {
			s(document).trigger("error", ["needLogin"])
		},
		reg: function() {
			s(document).trigger("error", ["needReg"])
		},
		stripTime: function(e) {
			return e.replace(/\+0800|\S\S\S\+0800|CST/g, "")
		},
		replace: function(e, t) {
			for (key in t) {
				if (Object.prototype.hasOwnProperty.call(t, key)) {
					e = e.replace(new RegExp(key, "g"), t[key])
				}
			}
			return e
		},
		getLength: function(e) {
			return Math.ceil(e.replace(/[\uFE30-\uFFA0\u2E80-\u9FFF\uac00-\ud7ff\u3000\u2018\u201c\u201d\u2019]/g, "**").length / 2)
		},
		getleftLength: function(e) {
			return Math.floor(e.replace(/[\uFE30-\uFFA0\u2E80-\u9FFF\uac00-\ud7ff\u3000\u2018\u201c\u201d\u2019]/g, "**").length / 2)
		},
		getsize: function(e) {
			return e.replace(/[\uFE30-\uFFA0\u2E80-\u9FFF\uac00-\ud7ff\u3000\u2018\u201c\u201d\u2019]/g, "**").length
		},
		isLegal: function(e) {
			for (var t = 0; t < e.length; t++) {
				if (e.charCodeAt(t) > 255) return false
			}
			return true
		},
		cutString: function(e, t, n) {
			var n = n * 2;
			if (!e || !n) {
				return ""
			}
			var i = 0;
			var r = 0;
			var s = "";
			for (r = t; r < e.length; r++) {
				if (e.charCodeAt(r) > 255) {
					i += 2
				} else {
					i++
				} if (i > n) {
					return s
				}
				s += e.charAt(r)
			}
			return e
		},
		subString: function(e, t, n) {
			var n = n * 2;
			if (!e || !n) {
				return ""
			}
			var i = 0;
			var r = 0;
			var s = "";
			for (r = t; r < e.length; r++) {
				if (e.charCodeAt(r) > 255) {
					i += 2
				} else {
					i++
				} if (i > n) {
					if (j.getleftLength(e) > n - 1) {
						s = s + "..."
					}
					return s
				}
				s += e.charAt(r)
			}
			if (j.getleftLength(e) > n - 1) {
				e = e + "..."
			}
			return e
		},
		formatTemplate: C,
		follow: function(e, t) {
			var n = t || function() {};
			s.post("/user/follow", {
				followId: e
			}, function(e) {
				if (e.status == 1) {
					t()
				} else {}
			})
		},
		defollow: function(e, t) {
			var n = t || function() {};
			s.post("/user/unfollow", {
				followId: e
			}, function(e) {
				if (e.status == 1) {
					t()
				} else {}
			})
		},
		bubbleNode: function(e, t, n) {
			var i = false;
			do {
				if (t(e)) {
					i = true;
					break
				} else {
					e = e.parentNode
				}
			} while (e.parentNode);
			i && n()
		},
		bubbleNodeNe: function(e, t, n) {
			var i = true;
			do {
				if (t(e)) {
					i = false;
					break
				} else {
					e = e.parentNode
				}
			} while (e.parentNode);
			i && n()
		},
		getSelectText: function() {
			if (document.selection) {
				return function() {
					return document.selection.createRange().text
				}
			} else {
				return function() {
					return document.getSelection()
				}
			}
		}(),
		getLinkParam: function(e) {
			return e.getAttribute("href").replace("javascript://", "")
		},
		getImageUrl: function(e, t, n, i) {
			var t = t,
				r = e.split(".")[1];
			var n = n || t;
			var s = 1;
			if (!i && i == 0) s = i;
			return "http://imgsize.ph.126.net/?imgurl=" + e + "_" + t + "x" + n + "x" + s + "x85." + r
		},
		formatDate: function(e, t) {
			var n = new Date(e);
			h = n.getFullYear();
			m = n.getMonth() + 1;
			g = n.getDate();
			v = n.getHours();
			y = n.getMinutes();
			b = n.getSeconds();
			return t.replace(/y+|m+|d+|h+|s+|H+|M+/g, x)
		},
		refresh: function() {
			window.location.reload()
		},
		wealth: {
			0: "\u5c4c\u4e1d",
			1: "\u4e00\u5bcc",
			2: "\u4e8c\u5bcc",
			3: "\u4e09\u5bcc",
			4: "\u56db\u5bcc",
			5: "\u4e94\u5bcc",
			6: "\u516d\u5bcc",
			7: "\u4e03\u5bcc",
			8: "\u516b\u5bcc",
			9: "\u4e5d\u5bcc",
			10: "\u5341\u5bcc",
			11: "\u7537\u7235",
			12: "\u5b50\u7235",
			13: "\u4f2f\u7235",
			14: "\u4faf\u7235",
			15: "\u516c\u7235",
			16: "\u90e1\u516c",
			17: "\u56fd\u516c",
			18: "\u738b\u7235",
			19: "\u85e9\u738b",
			20: "\u90e1\u738b",
			21: "\u4eb2\u738b",
			22: "\u56fd\u738b",
			23: "\u5e1d\u738b",
			24: "\u7687\u5e1d",
			25: "\u5929\u541b",
			26: "\u5e1d\u541b",
			27: "\u5723\u541b",
			28: "\u4e3b\u541b",
			29: "\u4ed9\u541b",
			30: "\u795e"
		},
		anchor: {
			0: "0\u661f",
			1: "1\u661f",
			2: "2\u661f",
			3: "3\u661f",
			4: "4\u661f",
			5: "5\u661f",
			6: "1\u94bb",
			7: "2\u94bb",
			8: "3\u94bb",
			9: "4\u94bb",
			10: "5\u94bb",
			11: "1\u7687\u51a0",
			12: "2\u7687\u51a0",
			13: "3\u7687\u51a0",
			14: "4\u7687\u51a0",
			15: "5\u7687\u51a0",
			16: "6\u7687\u51a0",
			17: "7\u7687\u51a0",
			18: "8\u7687\u51a0",
			19: "9\u7687\u51a0",
			20: "10\u7687\u51a0",
			21: "11\u7687\u51a0",
			22: "12\u7687\u51a0",
			23: "13\u7687\u51a0",
			24: "14\u7687\u51a0",
			25: "15\u7687\u51a0",
			26: "16\u7687\u51a0",
			27: "17\u7687\u51a0",
			28: "18\u7687\u51a0",
			29: "19\u7687\u51a0",
			30: "20\u7687\u51a0",
			31: "\u4e94\u5f69\u51a0",
			32: "\u4e94\u5f691\u661f",
			33: "\u4e94\u5f692\u661f",
			34: "\u4e94\u5f693\u661f",
			35: "\u4e94\u5f694\u661f",
			36: "\u4e94\u5f695\u661f",
			37: "\u4e94\u5f691\u94bb",
			38: "\u4e94\u5f692\u94bb",
			39: "\u4e94\u5f693\u94bb",
			40: "\u4e94\u5f694\u94bb",
			41: "\u4e94\u5f695\u94bb",
			42: "\u4e94\u5f691\u51a0",
			43: "\u4e94\u5f692\u51a0",
			44: "\u4e94\u5f693\u51a0",
			45: "\u4e94\u5f694\u51a0",
			46: "\u4e94\u5f695\u51a0",
			47: "\u4e94\u5f696\u51a0",
			48: "\u4e94\u5f697\u51a0",
			49: "\u4e94\u5f698\u51a0",
			50: "\u4e94\u5f699\u51a0",
			51: "\u4e94\u5f6910\u51a0",
			52: "\u4e94\u5f6911\u51a0",
			53: "\u4e94\u5f6912\u51a0",
			54: "\u4e94\u5f6913\u51a0",
			55: "\u4e94\u5f6914\u51a0",
			56: "\u4e94\u5f6915\u51a0",
			57: "\u4e94\u5f6916\u51a0",
			58: "\u4e94\u5f6917\u51a0",
			59: "\u4e94\u5f6918\u51a0",
			60: "\u4e94\u5f6919\u51a0",
			61: "\u4e94\u5f6920\u51a0",
			62: "\u9ec4\u91d1\u51a0",
			63: "\u9ec4\u91d11\u661f",
			64: "\u9ec4\u91d12\u661f",
			65: "\u9ec4\u91d13\u661f",
			66: "\u9ec4\u91d14\u661f",
			67: "\u9ec4\u91d15\u661f",
			68: "\u9ec4\u91d11\u94bb",
			69: "\u9ec4\u91d12\u94bb",
			70: "\u9ec4\u91d13\u94bb",
			71: "\u9ec4\u91d14\u94bb",
			72: "\u9ec4\u91d15\u94bb",
			73: "\u9ec4\u91d11\u51a0",
			74: "\u9ec4\u91d12\u51a0",
			75: "\u9ec4\u91d13\u51a0",
			76: "\u9ec4\u91d14\u51a0",
			77: "\u9ec4\u91d15\u51a0",
			78: "\u9ec4\u91d16\u51a0",
			79: "\u9ec4\u91d17\u51a0",
			80: "\u9ec4\u91d18\u51a0",
			81: "\u9ec4\u91d19\u51a0",
			82: "\u9ec4\u91d110\u51a0",
			83: "\u9ec4\u91d111\u51a0",
			84: "\u9ec4\u91d112\u51a0",
			85: "\u9ec4\u91d113\u51a0",
			86: "\u9ec4\u91d114\u51a0",
			87: "\u9ec4\u91d115\u51a0",
			88: "\u9ec4\u91d116\u51a0",
			89: "\u9ec4\u91d117\u51a0",
			90: "\u9ec4\u91d118\u51a0",
			91: "\u9ec4\u91d119\u51a0",
			92: "\u9ec4\u91d120\u51a0",
			93: "\u5b9d\u77f3\u51a0"
		}
	};
	j.EMAIL_REG = /^[a-zA-Z0-9_\-\.]{1,}@[a-zA-Z0-9_\-]{1,}\.[a-zA-Z0-9_\-.]{1,}$/;
	j.validateEmail = function(e) {
		if (typeof e !== "string") {
			return false
		}
		return j.EMAIL_REG.test(e)
	};
	j.countChars = function(e, t, n) {
		var i = e.replace(/[\u4e00-\u9fa5\s]/g, "**").length,
			r = [],
			s = 0;
		if (i <= t) {
			return e
		} else {
			for (var a = 0; a < i; a++) {
				var o = e.charAt(a);
				if (/[^\x00-\xff]/.test(o)) {
					s += 2
				} else {
					s += 1
				}
				r.push(o);
				if (s >= t) {
					break
				}
			}
			if (n) {
				return r.join("")
			} else {
				return r.join("") + "..."
			}
		}
	};
	j.encodeSpecialHtmlChar = function(e) {
		if (e) {
			var t = ["&", "<", ">", '"'];
			var n = ["&amp;", "&lt;", "&gt;", "&quot;"];
			var i = n.length;
			for (var r = 0; r < i; r++) {
				e = e.replace(new RegExp(t[r], "g"), n[r])
			}
			return e
		} else {
			return ""
		}
	};
	j.decodeSpecialHtmlChar = function(e) {
		if (e) {
			var t = ["&amp;", "&lt;", "&gt;", "&quot;", "&#quot", "&#rmrow", "&#lmrow", "&apos;"];
			var n = ["&", "<", ">", '"', "'", "(", ")", "'"];
			var i = n.length;
			for (var r = 0; r < i; r++) {
				e = e.replace(new RegExp(t[r], "g"), n[r])
			}
			return e
		} else {
			return ""
		}
	};
	j.formatNumber = function(e, n) {
		n = n > 0 && n <= 20 ? n : 2;
		e = parseFloat((e + "").replace(/[^\d\.-]/g, "")).toFixed(n) + "";
		var r = e.split(".")[0].split("").reverse(),
			s = e.split(".")[1];
		t = "";
		for (i = 0; i < r.length; i++) {
			t += r[i] + ((i + 1) % 3 == 0 && i + 1 != r.length ? "," : "")
		}
		return t.split("").reverse().join("")
	};
	j.recoverNumber = function(e) {
		return parseFloat(e.replace(/[^\d\.-]/g, ""))
	};
	j.delQueStr = function(e, t) {
		var n = "";
		if (e.indexOf("?") != -1) {
			n = e.substr(e.indexOf("?") + 1)
		} else {
			return e
		}
		var r = "";
		var s = "";
		var a = "";
		if (n.indexOf("&") != -1) {
			r = n.split("&");
			for (i in r) {
				if (r[i].split("=")[0] != t) {
					s = s + r[i].split("=")[0] + "=" + r[i].split("=")[1] + "&"
				}
			}
			return e.substr(0, e.indexOf("?")) + "?" + s.substr(0, s.length - 1)
		} else {
			r = n.split("=");
			if (r[0] == t) {
				return e.substr(0, e.indexOf("?"))
			} else {
				return e
			}
		}
	};
	j.getUrlStrs = function(e) {
		var t = [],
			n, i;
		if (e.indexOf("?") != -1) {
			i = e.substr(e.indexOf("?") + 1);
			var r = i.split("&")
		} else {
			var r = []
		}
		for (var s = 0; s < r.length; s++) {
			n = r[s].split("=");
			t.push(n[0]);
			t[n[0]] = n[1]
		}
		return t
	};
	j.getUrlStr = function(e, t) {
		return j.getUrlStrs(t)[e] || ""
	};
	return j
});
define("basic/cookie", function(e, t, n) {
	var i = e("basic/jquery");
	var r = function(e, t, n) {
		if (typeof t != "undefined") {
			n = n || {};
			if (t === null) {
				t = "";
				n.expires = -1
			}
			var r = "";
			if (n.expires && (typeof n.expires == "number" || n.expires.toUTCString)) {
				var s;
				if (typeof n.expires == "number") {
					s = new Date;
					s.setTime(s.getTime() + n.expires * 24 * 60 * 60 * 1e3)
				} else {
					s = n.expires
				}
				r = "; expires=" + s.toUTCString()
			}
			var a = n.path ? "; path=" + n.path : "";
			var o = n.domain ? "; domain=" + n.domain : "";
			var l = n.secure ? "; secure" : "";
			document.cookie = [e, "=", encodeURIComponent(t), r, a, o, l].join("")
		} else {
			var c = null;
			if (document.cookie && document.cookie != "") {
				var u = document.cookie.split(";");
				for (var f = 0; f < u.length; f++) {
					var d = i.trim(u[f]);
					if (d.substring(0, e.length + 1) == e + "=") {
						c = decodeURIComponent(d.substring(e.length + 1));
						break
					}
				}
			}
			return c
		}
	};
	return r
});
define("basic/jquery", function(e, t, n, i) {
	var r, s, a = window.document,
		o = window.location,
		l = window.navigator,
		c = window.jQuery,
		u = window.$,
		f = Array.prototype.push,
		d = Array.prototype.slice,
		p = Array.prototype.indexOf,
		h = Object.prototype.toString,
		m = Object.prototype.hasOwnProperty,
		g = String.prototype.trim,
		v = function(e, t) {
			return new v.fn.init(e, t, r)
		},
		y = /[\-+]?(?:\d*\.|)\d+(?:[eE][\-+]?\d+|)/.source,
		b = /\S/,
		w = /\s+/,
		x = /^[\s\uFEFF\xA0]+|[\s\uFEFF\xA0]+$/g,
		T = /^(?:[^#<]*(<[\w\W]+>)[^>]*$|#([\w\-]*)$)/,
		k = /^<(\w+)\s*\/?>(?:<\/\1>|)$/,
		C = /^[\],:{}\s]*$/,
		j = /(?:^|:|,)(?:\s*\[)+/g,
		E = /\\(?:["\\\/bfnrt]|u[\da-fA-F]{4})/g,
		S = /"[^"\\\r\n]*"|true|false|null|-?(?:\d\d*\.|)\d+(?:[eE][\-+]?\d+|)/g,
		N = /^-ms-/,
		L = /-([\da-z])/gi,
		I = function(e, t) {
			return (t + "").toUpperCase()
		},
		M = function() {
			if (a.addEventListener) {
				a.removeEventListener("DOMContentLoaded", M, false);
				v.ready()
			} else if (a.readyState === "complete") {
				a.detachEvent("onreadystatechange", M);
				v.ready()
			}
		},
		F = {};
	v.fn = v.prototype = {
		constructor: v,
		init: function(e, t, n) {
			var r, s, o, l;
			if (!e) {
				return this
			}
			if (e.nodeType) {
				this.context = this[0] = e;
				this.length = 1;
				return this
			}
			if (typeof e === "string") {
				if (e.charAt(0) === "<" && e.charAt(e.length - 1) === ">" && e.length >= 3) {
					r = [null, e, null]
				} else {
					r = T.exec(e)
				} if (r && (r[1] || !t)) {
					if (r[1]) {
						t = t instanceof v ? t[0] : t;
						l = t && t.nodeType ? t.ownerDocument || t : a;
						e = v.parseHTML(r[1], l, true);
						if (k.test(r[1]) && v.isPlainObject(t)) {
							this.attr.call(e, t, true)
						}
						return v.merge(this, e)
					} else {
						s = a.getElementById(r[2]);
						if (s && s.parentNode) {
							if (s.id !== r[2]) {
								return n.find(e)
							}
							this.length = 1;
							this[0] = s
						}
						this.context = a;
						this.selector = e;
						return this
					}
				} else if (!t || t.jquery) {
					return (t || n).find(e)
				} else {
					return this.constructor(t).find(e)
				}
			} else if (v.isFunction(e)) {
				return n.ready(e)
			}
			if (e.selector !== i) {
				this.selector = e.selector;
				this.context = e.context
			}
			return v.makeArray(e, this)
		},
		selector: "",
		jquery: "1.8.3",
		length: 0,
		size: function() {
			return this.length
		},
		toArray: function() {
			return d.call(this)
		},
		get: function(e) {
			return e == null ? this.toArray() : e < 0 ? this[this.length + e] : this[e]
		},
		pushStack: function(e, t, n) {
			var i = v.merge(this.constructor(), e);
			i.prevObject = this;
			i.context = this.context;
			if (t === "find") {
				i.selector = this.selector + (this.selector ? " " : "") + n
			} else if (t) {
				i.selector = this.selector + "." + t + "(" + n + ")"
			}
			return i
		},
		each: function(e, t) {
			return v.each(this, e, t)
		},
		ready: function(e) {
			v.ready.promise().done(e);
			return this
		},
		eq: function(e) {
			e = +e;
			return e === -1 ? this.slice(e) : this.slice(e, e + 1)
		},
		first: function() {
			return this.eq(0)
		},
		last: function() {
			return this.eq(-1)
		},
		slice: function() {
			return this.pushStack(d.apply(this, arguments), "slice", d.call(arguments).join(","))
		},
		map: function(e) {
			return this.pushStack(v.map(this, function(t, n) {
				return e.call(t, n, t)
			}))
		},
		end: function() {
			return this.prevObject || this.constructor(null)
		},
		push: f,
		sort: [].sort,
		splice: [].splice
	};
	v.fn.init.prototype = v.fn;
	v.extend = v.fn.extend = function() {
		var e, t, n, r, s, a, o = arguments[0] || {},
			l = 1,
			c = arguments.length,
			u = false;
		if (typeof o === "boolean") {
			u = o;
			o = arguments[1] || {};
			l = 2
		}
		if (typeof o !== "object" && !v.isFunction(o)) {
			o = {}
		}
		if (c === l) {
			o = this;
			--l
		}
		for (; l < c; l++) {
			if ((e = arguments[l]) != null) {
				for (t in e) {
					n = o[t];
					r = e[t];
					if (o === r) {
						continue
					}
					if (u && r && (v.isPlainObject(r) || (s = v.isArray(r)))) {
						if (s) {
							s = false;
							a = n && v.isArray(n) ? n : []
						} else {
							a = n && v.isPlainObject(n) ? n : {}
						}
						o[t] = v.extend(u, a, r)
					} else if (r !== i) {
						o[t] = r
					}
				}
			}
		}
		return o
	};
	v.extend({
		noConflict: function(e) {
			if (window.$ === v) {
				window.$ = u
			}
			if (e && window.jQuery === v) {
				window.jQuery = c
			}
			return v
		},
		isReady: false,
		readyWait: 1,
		holdReady: function(e) {
			if (e) {
				v.readyWait++
			} else {
				v.ready(true)
			}
		},
		ready: function(e) {
			if (e === true ? --v.readyWait : v.isReady) {
				return
			}
			if (!a.body) {
				return setTimeout(v.ready, 1)
			}
			v.isReady = true;
			if (e !== true && --v.readyWait > 0) {
				return
			}
			s.resolveWith(a, [v]);
			if (v.fn.trigger) {
				v(a).trigger("ready").off("ready")
			}
		},
		isFunction: function(e) {
			return v.type(e) === "function"
		},
		isArray: Array.isArray || function(e) {
			return v.type(e) === "array"
		},
		isWindow: function(e) {
			return e != null && e == e.window
		},
		isNumeric: function(e) {
			return !isNaN(parseFloat(e)) && isFinite(e)
		},
		type: function(e) {
			return e == null ? String(e) : F[h.call(e)] || "object"
		},
		isPlainObject: function(e) {
			if (!e || v.type(e) !== "object" || e.nodeType || v.isWindow(e)) {
				return false
			}
			try {
				if (e.constructor && !m.call(e, "constructor") && !m.call(e.constructor.prototype, "isPrototypeOf")) {
					return false
				}
			} catch (t) {
				return false
			}
			var n;
			for (n in e) {}
			return n === i || m.call(e, n)
		},
		isEmptyObject: function(e) {
			var t;
			for (t in e) {
				return false
			}
			return true
		},
		error: function(e) {
			throw new Error(e)
		},
		parseHTML: function(e, t, n) {
			var i;
			if (!e || typeof e !== "string") {
				return null
			}
			if (typeof t === "boolean") {
				n = t;
				t = 0
			}
			t = t || a;
			if (i = k.exec(e)) {
				return [t.createElement(i[1])]
			}
			i = v.buildFragment([e], t, n ? null : []);
			return v.merge([], (i.cacheable ? v.clone(i.fragment) : i.fragment).childNodes)
		},
		parseJSON: function(e) {
			if (!e || typeof e !== "string") {
				return null
			}
			e = v.trim(e);
			if (window.JSON && window.JSON.parse) {
				return window.JSON.parse(e)
			}
			if (C.test(e.replace(E, "@").replace(S, "]").replace(j, ""))) {
				return new Function("return " + e)()
			}
			v.error("Invalid JSON: " + e)
		},
		parseXML: function(e) {
			var t, n;
			if (!e || typeof e !== "string") {
				return null
			}
			try {
				if (window.DOMParser) {
					n = new DOMParser;
					t = n.parseFromString(e, "text/xml")
				} else {
					t = new ActiveXObject("Microsoft.XMLDOM");
					t.async = "false";
					t.loadXML(e)
				}
			} catch (r) {
				t = i
			}
			if (!t || !t.documentElement || t.getElementsByTagName("parsererror").length) {
				v.error("Invalid XML: " + e)
			}
			return t
		},
		noop: function() {},
		globalEval: function(e) {
			if (e && b.test(e)) {
				(window.execScript || function(e) {
					window["eval"].call(window, e)
				})(e)
			}
		},
		camelCase: function(e) {
			return e.replace(N, "ms-").replace(L, I)
		},
		nodeName: function(e, t) {
			return e.nodeName && e.nodeName.toLowerCase() === t.toLowerCase()
		},
		each: function(e, t, n) {
			var r, s = 0,
				a = e.length,
				o = a === i || v.isFunction(e);
			if (n) {
				if (o) {
					for (r in e) {
						if (t.apply(e[r], n) === false) {
							break
						}
					}
				} else {
					for (; s < a;) {
						if (t.apply(e[s++], n) === false) {
							break
						}
					}
				}
			} else {
				if (o) {
					for (r in e) {
						if (t.call(e[r], r, e[r]) === false) {
							break
						}
					}
				} else {
					for (; s < a;) {
						if (t.call(e[s], s, e[s++]) === false) {
							break
						}
					}
				}
			}
			return e
		},
		trim: g && !g.call("\ufeff\xa0") ? function(e) {
			return e == null ? "" : g.call(e)
		} : function(e) {
			return e == null ? "" : (e + "").replace(x, "")
		},
		makeArray: function(e, t) {
			var n, i = t || [];
			if (e != null) {
				n = v.type(e);
				if (e.length == null || n === "string" || n === "function" || n === "regexp" || v.isWindow(e)) {
					f.call(i, e)
				} else {
					v.merge(i, e)
				}
			}
			return i
		},
		inArray: function(e, t, n) {
			var i;
			if (t) {
				if (p) {
					return p.call(t, e, n)
				}
				i = t.length;
				n = n ? n < 0 ? Math.max(0, i + n) : n : 0;
				for (; n < i; n++) {
					if (n in t && t[n] === e) {
						return n
					}
				}
			}
			return -1
		},
		merge: function(e, t) {
			var n = t.length,
				r = e.length,
				s = 0;
			if (typeof n === "number") {
				for (; s < n; s++) {
					e[r++] = t[s]
				}
			} else {
				while (t[s] !== i) {
					e[r++] = t[s++]
				}
			}
			e.length = r;
			return e
		},
		grep: function(e, t, n) {
			var i, r = [],
				s = 0,
				a = e.length;
			n = !!n;
			for (; s < a; s++) {
				i = !!t(e[s], s);
				if (n !== i) {
					r.push(e[s])
				}
			}
			return r
		},
		map: function(e, t, n) {
			var r, s, a = [],
				o = 0,
				l = e.length,
				c = e instanceof v || l !== i && typeof l === "number" && (l > 0 && e[0] && e[l - 1] || l === 0 || v.isArray(e));
			if (c) {
				for (; o < l; o++) {
					r = t(e[o], o, n);
					if (r != null) {
						a[a.length] = r
					}
				}
			} else {
				for (s in e) {
					r = t(e[s], s, n);
					if (r != null) {
						a[a.length] = r
					}
				}
			}
			return a.concat.apply([], a)
		},
		guid: 1,
		proxy: function(e, t) {
			var n, r, s;
			if (typeof t === "string") {
				n = e[t];
				t = e;
				e = n
			}
			if (!v.isFunction(e)) {
				return i
			}
			r = d.call(arguments, 2);
			s = function() {
				return e.apply(t, r.concat(d.call(arguments)))
			};
			s.guid = e.guid = e.guid || v.guid++;
			return s
		},
		access: function(e, t, n, r, s, a, o) {
			var l, c = n == null,
				u = 0,
				f = e.length;
			if (n && typeof n === "object") {
				for (u in n) {
					v.access(e, t, u, n[u], 1, a, r)
				}
				s = 1
			} else if (r !== i) {
				l = o === i && v.isFunction(r);
				if (c) {
					if (l) {
						l = t;
						t = function(e, t, n) {
							return l.call(v(e), n)
						}
					} else {
						t.call(e, r);
						t = null
					}
				}
				if (t) {
					for (; u < f; u++) {
						t(e[u], n, l ? r.call(e[u], u, t(e[u], n)) : r, o)
					}
				}
				s = 1
			}
			return s ? e : c ? t.call(e) : f ? t(e[0], n) : a
		},
		now: function() {
			return (new Date).getTime()
		}
	});
	v.ready.promise = function(e) {
		if (!s) {
			s = v.Deferred();
			if (a.readyState === "complete") {
				setTimeout(v.ready, 1)
			} else if (a.addEventListener) {
				a.addEventListener("DOMContentLoaded", M, false);
				window.addEventListener("load", v.ready, false)
			} else {
				a.attachEvent("onreadystatechange", M);
				window.attachEvent("onload", v.ready);
				var t = false;
				try {
					t = window.frameElement == null && a.documentElement
				} catch (n) {}
				if (t && t.doScroll) {
					(function i() {
						if (!v.isReady) {
							try {
								t.doScroll("left")
							} catch (e) {
								return setTimeout(i, 50)
							}
							v.ready()
						}
					})()
				}
			}
		}
		return s.promise(e)
	};
	v.each("Boolean Number String Function Array Date RegExp Object".split(" "), function(e, t) {
		F["[object " + t + "]"] = t.toLowerCase()
	});
	r = v(a);
	var _ = {};

	function A(e) {
		var t = _[e] = {};
		v.each(e.split(w), function(e, n) {
			t[n] = true
		});
		return t
	}
	v.Callbacks = function(e) {
		e = typeof e === "string" ? _[e] || A(e) : v.extend({}, e);
		var t, n, r, s, a, o, l = [],
			c = !e.once && [],
			u = function(i) {
				t = e.memory && i;
				n = true;
				o = s || 0;
				s = 0;
				a = l.length;
				r = true;
				for (; l && o < a; o++) {
					if (l[o].apply(i[0], i[1]) === false && e.stopOnFalse) {
						t = false;
						break
					}
				}
				r = false;
				if (l) {
					if (c) {
						if (c.length) {
							u(c.shift())
						}
					} else if (t) {
						l = []
					} else {
						f.disable()
					}
				}
			},
			f = {
				add: function() {
					if (l) {
						var n = l.length;
						(function i(t) {
							v.each(t, function(t, n) {
								var r = v.type(n);
								if (r === "function") {
									if (!e.unique || !f.has(n)) {
										l.push(n)
									}
								} else if (n && n.length && r !== "string") {
									i(n)
								}
							})
						})(arguments);
						if (r) {
							a = l.length
						} else if (t) {
							s = n;
							u(t)
						}
					}
					return this
				},
				remove: function() {
					if (l) {
						v.each(arguments, function(e, t) {
							var n;
							while ((n = v.inArray(t, l, n)) > -1) {
								l.splice(n, 1);
								if (r) {
									if (n <= a) {
										a--
									}
									if (n <= o) {
										o--
									}
								}
							}
						})
					}
					return this
				},
				has: function(e) {
					return v.inArray(e, l) > -1
				},
				empty: function() {
					l = [];
					return this
				},
				disable: function() {
					l = c = t = i;
					return this
				},
				disabled: function() {
					return !l
				},
				lock: function() {
					c = i;
					if (!t) {
						f.disable()
					}
					return this
				},
				locked: function() {
					return !c
				},
				fireWith: function(e, t) {
					t = t || [];
					t = [e, t.slice ? t.slice() : t];
					if (l && (!n || c)) {
						if (r) {
							c.push(t)
						} else {
							u(t)
						}
					}
					return this
				},
				fire: function() {
					f.fireWith(this, arguments);
					return this
				},
				fired: function() {
					return !!n
				}
			};
		return f
	};
	v.extend({
		Deferred: function(e) {
			var t = [
					["resolve", "done", v.Callbacks("once memory"), "resolved"],
					["reject", "fail", v.Callbacks("once memory"), "rejected"],
					["notify", "progress", v.Callbacks("memory")]
				],
				n = "pending",
				i = {
					state: function() {
						return n
					},
					always: function() {
						r.done(arguments).fail(arguments);
						return this
					},
					then: function() {
						var e = arguments;
						return v.Deferred(function(n) {
							v.each(t, function(t, i) {
								var s = i[0],
									a = e[t];
								r[i[1]](v.isFunction(a) ? function() {
									var e = a.apply(this, arguments);
									if (e && v.isFunction(e.promise)) {
										e.promise().done(n.resolve).fail(n.reject).progress(n.notify)
									} else {
										n[s + "With"](this === r ? n : this, [e])
									}
								} : n[s])
							});
							e = null
						}).promise()
					},
					promise: function(e) {
						return e != null ? v.extend(e, i) : i
					}
				},
				r = {};
			i.pipe = i.then;
			v.each(t, function(e, s) {
				var a = s[2],
					o = s[3];
				i[s[1]] = a.add;
				if (o) {
					a.add(function() {
						n = o
					}, t[e ^ 1][2].disable, t[2][2].lock)
				}
				r[s[0]] = a.fire;
				r[s[0] + "With"] = a.fireWith
			});
			i.promise(r);
			if (e) {
				e.call(r, r)
			}
			return r
		},
		when: function(e) {
			var t = 0,
				n = d.call(arguments),
				i = n.length,
				r = i !== 1 || e && v.isFunction(e.promise) ? i : 0,
				s = r === 1 ? e : v.Deferred(),
				a = function(e, t, n) {
					return function(i) {
						t[e] = this;
						n[e] = arguments.length > 1 ? d.call(arguments) : i;
						if (n === o) {
							s.notifyWith(t, n)
						} else if (!--r) {
							s.resolveWith(t, n)
						}
					}
				},
				o, l, c;
			if (i > 1) {
				o = new Array(i);
				l = new Array(i);
				c = new Array(i);
				for (; t < i; t++) {
					if (n[t] && v.isFunction(n[t].promise)) {
						n[t].promise().done(a(t, c, n)).fail(s.reject).progress(a(t, l, o))
					} else {
						--r
					}
				}
			}
			if (!r) {
				s.resolveWith(c, n)
			}
			return s.promise()
		}
	});
	v.support = function() {
		var e, t, n, i, r, s, o, l, c, u, f, d = a.createElement("div");
		d.setAttribute("className", "t");
		d.innerHTML = "  <link/><table></table><a href='/a'>a</a><input type='checkbox'/>";
		t = d.getElementsByTagName("*");
		n = d.getElementsByTagName("a")[0];
		if (!t || !n || !t.length) {
			return {}
		}
		i = a.createElement("select");
		r = i.appendChild(a.createElement("option"));
		s = d.getElementsByTagName("input")[0];
		n.style.cssText = "top:1px;float:left;opacity:.5";
		e = {
			leadingWhitespace: d.firstChild.nodeType === 3,
			tbody: !d.getElementsByTagName("tbody").length,
			htmlSerialize: !!d.getElementsByTagName("link").length,
			style: /top/.test(n.getAttribute("style")),
			hrefNormalized: n.getAttribute("href") === "/a",
			opacity: /^0.5/.test(n.style.opacity),
			cssFloat: !!n.style.cssFloat,
			checkOn: s.value === "on",
			optSelected: r.selected,
			getSetAttribute: d.className !== "t",
			enctype: !!a.createElement("form").enctype,
			html5Clone: a.createElement("nav").cloneNode(true).outerHTML !== "<:nav></:nav>",
			boxModel: a.compatMode === "CSS1Compat",
			submitBubbles: true,
			changeBubbles: true,
			focusinBubbles: false,
			deleteExpando: true,
			noCloneEvent: true,
			inlineBlockNeedsLayout: false,
			shrinkWrapBlocks: false,
			reliableMarginRight: true,
			boxSizingReliable: true,
			pixelPosition: false
		};
		s.checked = true;
		e.noCloneChecked = s.cloneNode(true).checked;
		i.disabled = true;
		e.optDisabled = !r.disabled;
		try {
			delete d.test
		} catch (p) {
			e.deleteExpando = false
		}
		if (!d.addEventListener && d.attachEvent && d.fireEvent) {
			d.attachEvent("onclick", f = function() {
				e.noCloneEvent = false
			});
			d.cloneNode(true).fireEvent("onclick");
			d.detachEvent("onclick", f)
		}
		s = a.createElement("input");
		s.value = "t";
		s.setAttribute("type", "radio");
		e.radioValue = s.value === "t";
		s.setAttribute("checked", "checked");
		s.setAttribute("name", "t");
		d.appendChild(s);
		o = a.createDocumentFragment();
		o.appendChild(d.lastChild);
		e.checkClone = o.cloneNode(true).cloneNode(true).lastChild.checked;
		e.appendChecked = s.checked;
		o.removeChild(s);
		o.appendChild(d);
		if (d.attachEvent) {
			for (c in {
				submit: true,
				change: true,
				focusin: true
			}) {
				l = "on" + c;
				u = l in d;
				if (!u) {
					d.setAttribute(l, "return;");
					u = typeof d[l] === "function"
				}
				e[c + "Bubbles"] = u
			}
		}
		v(function() {
			var t, n, i, r, s = "padding:0;margin:0;border:0;display:block;overflow:hidden;",
				o = a.getElementsByTagName("body")[0];
			if (!o) {
				return
			}
			t = a.createElement("div");
			t.style.cssText = "visibility:hidden;border:0;width:0;height:0;position:static;top:0;margin-top:1px";
			o.insertBefore(t, o.firstChild);
			n = a.createElement("div");
			t.appendChild(n);
			n.innerHTML = "<table><tr><td></td><td>t</td></tr></table>";
			i = n.getElementsByTagName("td");
			i[0].style.cssText = "padding:0;margin:0;border:0;display:none";
			u = i[0].offsetHeight === 0;
			i[0].style.display = "";
			i[1].style.display = "none";
			e.reliableHiddenOffsets = u && i[0].offsetHeight === 0;
			n.innerHTML = "";
			n.style.cssText = "box-sizing:border-box;-moz-box-sizing:border-box;-webkit-box-sizing:border-box;padding:1px;border:1px;display:block;width:4px;margin-top:1%;position:absolute;top:1%;";
			e.boxSizing = n.offsetWidth === 4;
			e.doesNotIncludeMarginInBodyOffset = o.offsetTop !== 1;
			if (window.getComputedStyle) {
				e.pixelPosition = (window.getComputedStyle(n, null) || {}).top !== "1%";
				e.boxSizingReliable = (window.getComputedStyle(n, null) || {
					width: "4px"
				}).width === "4px";
				r = a.createElement("div");
				r.style.cssText = n.style.cssText = s;
				r.style.marginRight = r.style.width = "0";
				n.style.width = "1px";
				n.appendChild(r);
				e.reliableMarginRight = !parseFloat((window.getComputedStyle(r, null) || {}).marginRight)
			}
			if (typeof n.style.zoom !== "undefined") {
				n.innerHTML = "";
				n.style.cssText = s + "width:1px;padding:1px;display:inline;zoom:1";
				e.inlineBlockNeedsLayout = n.offsetWidth === 3;
				n.style.display = "block";
				n.style.overflow = "visible";
				n.innerHTML = "<div></div>";
				n.firstChild.style.width = "5px";
				e.shrinkWrapBlocks = n.offsetWidth !== 3;
				t.style.zoom = 1
			}
			o.removeChild(t);
			t = n = i = r = null
		});
		o.removeChild(d);
		t = n = i = r = s = o = d = null;
		return e
	}();
	var $ = /(?:\{[\s\S]*\}|\[[\s\S]*\])$/,
		B = /([A-Z])/g;
	v.extend({
		cache: {},
		deletedIds: [],
		uuid: 0,
		expando: "jQuery" + (v.fn.jquery + Math.random()).replace(/\D/g, ""),
		noData: {
			embed: true,
			object: "clsid:D27CDB6E-AE6D-11cf-96B8-444553540000",
			applet: true
		},
		hasData: function(e) {
			e = e.nodeType ? v.cache[e[v.expando]] : e[v.expando];
			return !!e && !R(e)
		},
		data: function(e, t, n, r) {
			if (!v.acceptData(e)) {
				return
			}
			var s, a, o = v.expando,
				l = typeof t === "string",
				c = e.nodeType,
				u = c ? v.cache : e,
				f = c ? e[o] : e[o] && o;
			if ((!f || !u[f] || !r && !u[f].data) && l && n === i) {
				return
			}
			if (!f) {
				if (c) {
					e[o] = f = v.deletedIds.pop() || v.guid++
				} else {
					f = o
				}
			}
			if (!u[f]) {
				u[f] = {};
				if (!c) {
					u[f].toJSON = v.noop
				}
			}
			if (typeof t === "object" || typeof t === "function") {
				if (r) {
					u[f] = v.extend(u[f], t)
				} else {
					u[f].data = v.extend(u[f].data, t)
				}
			}
			s = u[f];
			if (!r) {
				if (!s.data) {
					s.data = {}
				}
				s = s.data
			}
			if (n !== i) {
				s[v.camelCase(t)] = n
			}
			if (l) {
				a = s[t];
				if (a == null) {
					a = s[v.camelCase(t)]
				}
			} else {
				a = s
			}
			return a
		},
		removeData: function(e, t, n) {
			if (!v.acceptData(e)) {
				return
			}
			var i, r, s, a = e.nodeType,
				o = a ? v.cache : e,
				l = a ? e[v.expando] : v.expando;
			if (!o[l]) {
				return
			}
			if (t) {
				i = n ? o[l] : o[l].data;
				if (i) {
					if (!v.isArray(t)) {
						if (t in i) {
							t = [t]
						} else {
							t = v.camelCase(t);
							if (t in i) {
								t = [t]
							} else {
								t = t.split(" ")
							}
						}
					}
					for (r = 0, s = t.length; r < s; r++) {
						delete i[t[r]]
					}
					if (!(n ? R : v.isEmptyObject)(i)) {
						return
					}
				}
			}
			if (!n) {
				delete o[l].data;
				if (!R(o[l])) {
					return
				}
			}
			if (a) {
				v.cleanData([e], true)
			} else if (v.support.deleteExpando || o != o.window) {
				delete o[l]
			} else {
				o[l] = null
			}
		},
		_data: function(e, t, n) {
			return v.data(e, t, n, true)
		},
		acceptData: function(e) {
			var t = e.nodeName && v.noData[e.nodeName.toLowerCase()];
			return !t || t !== true && e.getAttribute("classid") === t
		}
	});
	v.fn.extend({
		data: function(e, t) {
			var n, r, s, a, o, l = this[0],
				c = 0,
				u = null;
			if (e === i) {
				if (this.length) {
					u = v.data(l);
					if (l.nodeType === 1 && !v._data(l, "parsedAttrs")) {
						s = l.attributes;
						for (o = s.length; c < o; c++) {
							a = s[c].name;
							if (!a.indexOf("data-")) {
								a = v.camelCase(a.substring(5));
								D(l, a, u[a])
							}
						}
						v._data(l, "parsedAttrs", true)
					}
				}
				return u
			}
			if (typeof e === "object") {
				return this.each(function() {
					v.data(this, e)
				})
			}
			n = e.split(".", 2);
			n[1] = n[1] ? "." + n[1] : "";
			r = n[1] + "!";
			return v.access(this, function(t) {
				if (t === i) {
					u = this.triggerHandler("getData" + r, [n[0]]);
					if (u === i && l) {
						u = v.data(l, e);
						u = D(l, e, u)
					}
					return u === i && n[1] ? this.data(n[0]) : u
				}
				n[1] = t;
				this.each(function() {
					var i = v(this);
					i.triggerHandler("setData" + r, n);
					v.data(this, e, t);
					i.triggerHandler("changeData" + r, n)
				})
			}, null, t, arguments.length > 1, null, false)
		},
		removeData: function(e) {
			return this.each(function() {
				v.removeData(this, e)
			})
		}
	});

	function D(e, t, n) {
		if (n === i && e.nodeType === 1) {
			var r = "data-" + t.replace(B, "-$1").toLowerCase();
			n = e.getAttribute(r);
			if (typeof n === "string") {
				try {
					n = n === "true" ? true : n === "false" ? false : n === "null" ? null : +n + "" === n ? +n : $.test(n) ? v.parseJSON(n) : n
				} catch (s) {}
				v.data(e, t, n)
			} else {
				n = i
			}
		}
		return n
	}

	function R(e) {
		var t;
		for (t in e) {
			if (t === "data" && v.isEmptyObject(e[t])) {
				continue
			}
			if (t !== "toJSON") {
				return false
			}
		}
		return true
	}
	v.extend({
		queue: function(e, t, n) {
			var i;
			if (e) {
				t = (t || "fx") + "queue";
				i = v._data(e, t);
				if (n) {
					if (!i || v.isArray(n)) {
						i = v._data(e, t, v.makeArray(n))
					} else {
						i.push(n)
					}
				}
				return i || []
			}
		},
		dequeue: function(e, t) {
			t = t || "fx";
			var n = v.queue(e, t),
				i = n.length,
				r = n.shift(),
				s = v._queueHooks(e, t),
				a = function() {
					v.dequeue(e, t)
				};
			if (r === "inprogress") {
				r = n.shift();
				i--
			}
			if (r) {
				if (t === "fx") {
					n.unshift("inprogress")
				}
				delete s.stop;
				r.call(e, a, s)
			}
			if (!i && s) {
				s.empty.fire()
			}
		},
		_queueHooks: function(e, t) {
			var n = t + "queueHooks";
			return v._data(e, n) || v._data(e, n, {
				empty: v.Callbacks("once memory").add(function() {
					v.removeData(e, t + "queue", true);
					v.removeData(e, n, true)
				})
			})
		}
	});
	v.fn.extend({
		queue: function(e, t) {
			var n = 2;
			if (typeof e !== "string") {
				t = e;
				e = "fx";
				n--
			}
			if (arguments.length < n) {
				return v.queue(this[0], e)
			}
			return t === i ? this : this.each(function() {
				var n = v.queue(this, e, t);
				v._queueHooks(this, e);
				if (e === "fx" && n[0] !== "inprogress") {
					v.dequeue(this, e)
				}
			})
		},
		dequeue: function(e) {
			return this.each(function() {
				v.dequeue(this, e)
			})
		},
		delay: function(e, t) {
			e = v.fx ? v.fx.speeds[e] || e : e;
			t = t || "fx";
			return this.queue(t, function(t, n) {
				var i = setTimeout(t, e);
				n.stop = function() {
					clearTimeout(i)
				}
			})
		},
		clearQueue: function(e) {
			return this.queue(e || "fx", [])
		},
		promise: function(e, t) {
			var n, r = 1,
				s = v.Deferred(),
				a = this,
				o = this.length,
				l = function() {
					if (!--r) {
						s.resolveWith(a, [a])
					}
				};
			if (typeof e !== "string") {
				t = e;
				e = i
			}
			e = e || "fx";
			while (o--) {
				n = v._data(a[o], e + "queueHooks");
				if (n && n.empty) {
					r++;
					n.empty.add(l)
				}
			}
			l();
			return s.promise(t)
		}
	});
	var H, P, O, W = /[\t\r\n]/g,
		q = /\r/g,
		U = /^(?:button|input)$/i,
		z = /^(?:button|input|object|select|textarea)$/i,
		V = /^a(?:rea|)$/i,
		G = /^(?:autofocus|autoplay|async|checked|controls|defer|disabled|hidden|loop|multiple|open|readonly|required|scoped|selected)$/i,
		X = v.support.getSetAttribute;
	v.fn.extend({
		attr: function(e, t) {
			return v.access(this, v.attr, e, t, arguments.length > 1)
		},
		removeAttr: function(e) {
			return this.each(function() {
				v.removeAttr(this, e)
			})
		},
		prop: function(e, t) {
			return v.access(this, v.prop, e, t, arguments.length > 1)
		},
		removeProp: function(e) {
			e = v.propFix[e] || e;
			return this.each(function() {
				try {
					this[e] = i;
					delete this[e]
				} catch (t) {}
			})
		},
		addClass: function(e) {
			var t, n, i, r, s, a, o;
			if (v.isFunction(e)) {
				return this.each(function(t) {
					v(this).addClass(e.call(this, t, this.className))
				})
			}
			if (e && typeof e === "string") {
				t = e.split(w);
				for (n = 0, i = this.length; n < i; n++) {
					r = this[n];
					if (r.nodeType === 1) {
						if (!r.className && t.length === 1) {
							r.className = e
						} else {
							s = " " + r.className + " ";
							for (a = 0, o = t.length; a < o; a++) {
								if (s.indexOf(" " + t[a] + " ") < 0) {
									s += t[a] + " "
								}
							}
							r.className = v.trim(s)
						}
					}
				}
			}
			return this
		},
		removeClass: function(e) {
			var t, n, r, s, a, o, l;
			if (v.isFunction(e)) {
				return this.each(function(t) {
					v(this).removeClass(e.call(this, t, this.className))
				})
			}
			if (e && typeof e === "string" || e === i) {
				t = (e || "").split(w);
				for (o = 0, l = this.length; o < l; o++) {
					r = this[o];
					if (r.nodeType === 1 && r.className) {
						n = (" " + r.className + " ").replace(W, " ");
						for (s = 0, a = t.length; s < a; s++) {
							while (n.indexOf(" " + t[s] + " ") >= 0) {
								n = n.replace(" " + t[s] + " ", " ")
							}
						}
						r.className = e ? v.trim(n) : ""
					}
				}
			}
			return this
		},
		toggleClass: function(e, t) {
			var n = typeof e,
				i = typeof t === "boolean";
			if (v.isFunction(e)) {
				return this.each(function(n) {
					v(this).toggleClass(e.call(this, n, this.className, t), t)
				})
			}
			return this.each(function() {
				if (n === "string") {
					var r, s = 0,
						a = v(this),
						o = t,
						l = e.split(w);
					while (r = l[s++]) {
						o = i ? o : !a.hasClass(r);
						a[o ? "addClass" : "removeClass"](r)
					}
				} else if (n === "undefined" || n === "boolean") {
					if (this.className) {
						v._data(this, "__className__", this.className)
					}
					this.className = this.className || e === false ? "" : v._data(this, "__className__") || ""
				}
			})
		},
		hasClass: function(e) {
			var t = " " + e + " ",
				n = 0,
				i = this.length;
			for (; n < i; n++) {
				if (this[n].nodeType === 1 && (" " + this[n].className + " ").replace(W, " ").indexOf(t) >= 0) {
					return true
				}
			}
			return false
		},
		val: function(e) {
			var t, n, r, s = this[0];
			if (!arguments.length) {
				if (s) {
					t = v.valHooks[s.type] || v.valHooks[s.nodeName.toLowerCase()];
					if (t && "get" in t && (n = t.get(s, "value")) !== i) {
						return n
					}
					n = s.value;
					return typeof n === "string" ? n.replace(q, "") : n == null ? "" : n
				}
				return
			}
			r = v.isFunction(e);
			return this.each(function(n) {
				var s, a = v(this);
				if (this.nodeType !== 1) {
					return
				}
				if (r) {
					s = e.call(this, n, a.val())
				} else {
					s = e
				} if (s == null) {
					s = ""
				} else if (typeof s === "number") {
					s += ""
				} else if (v.isArray(s)) {
					s = v.map(s, function(e) {
						return e == null ? "" : e + ""
					})
				}
				t = v.valHooks[this.type] || v.valHooks[this.nodeName.toLowerCase()];
				if (!t || !("set" in t) || t.set(this, s, "value") === i) {
					this.value = s
				}
			})
		}
	});
	v.extend({
		valHooks: {
			option: {
				get: function(e) {
					var t = e.attributes.value;
					return !t || t.specified ? e.value : e.text
				}
			},
			select: {
				get: function(e) {
					var t, n, i = e.options,
						r = e.selectedIndex,
						s = e.type === "select-one" || r < 0,
						a = s ? null : [],
						o = s ? r + 1 : i.length,
						l = r < 0 ? o : s ? r : 0;
					for (; l < o; l++) {
						n = i[l];
						if ((n.selected || l === r) && (v.support.optDisabled ? !n.disabled : n.getAttribute("disabled") === null) && (!n.parentNode.disabled || !v.nodeName(n.parentNode, "optgroup"))) {
							t = v(n).val();
							if (s) {
								return t
							}
							a.push(t)
						}
					}
					return a
				},
				set: function(e, t) {
					var n = v.makeArray(t);
					v(e).find("option").each(function() {
						this.selected = v.inArray(v(this).val(), n) >= 0
					});
					if (!n.length) {
						e.selectedIndex = -1
					}
					return n
				}
			}
		},
		attrFn: {},
		attr: function(e, t, n, r) {
			var s, a, o, l = e.nodeType;
			if (!e || l === 3 || l === 8 || l === 2) {
				return
			}
			if (r && v.isFunction(v.fn[t])) {
				return v(e)[t](n)
			}
			if (typeof e.getAttribute === "undefined") {
				return v.prop(e, t, n)
			}
			o = l !== 1 || !v.isXMLDoc(e);
			if (o) {
				t = t.toLowerCase();
				a = v.attrHooks[t] || (G.test(t) ? P : H)
			}
			if (n !== i) {
				if (n === null) {
					v.removeAttr(e, t);
					return
				} else if (a && "set" in a && o && (s = a.set(e, n, t)) !== i) {
					return s
				} else {
					e.setAttribute(t, n + "");
					return n
				}
			} else if (a && "get" in a && o && (s = a.get(e, t)) !== null) {
				return s
			} else {
				s = e.getAttribute(t);
				return s === null ? i : s
			}
		},
		removeAttr: function(e, t) {
			var n, i, r, s, a = 0;
			if (t && e.nodeType === 1) {
				i = t.split(w);
				for (; a < i.length; a++) {
					r = i[a];
					if (r) {
						n = v.propFix[r] || r;
						s = G.test(r);
						if (!s) {
							v.attr(e, r, "")
						}
						e.removeAttribute(X ? r : n);
						if (s && n in e) {
							e[n] = false
						}
					}
				}
			}
		},
		attrHooks: {
			type: {
				set: function(e, t) {
					if (U.test(e.nodeName) && e.parentNode) {
						v.error("type property can't be changed")
					} else if (!v.support.radioValue && t === "radio" && v.nodeName(e, "input")) {
						var n = e.value;
						e.setAttribute("type", t);
						if (n) {
							e.value = n
						}
						return t
					}
				}
			},
			value: {
				get: function(e, t) {
					if (H && v.nodeName(e, "button")) {
						return H.get(e, t)
					}
					return t in e ? e.value : null
				},
				set: function(e, t, n) {
					if (H && v.nodeName(e, "button")) {
						return H.set(e, t, n)
					}
					e.value = t
				}
			}
		},
		propFix: {
			tabindex: "tabIndex",
			readonly: "readOnly",
			"for": "htmlFor",
			"class": "className",
			maxlength: "maxLength",
			cellspacing: "cellSpacing",
			cellpadding: "cellPadding",
			rowspan: "rowSpan",
			colspan: "colSpan",
			usemap: "useMap",
			frameborder: "frameBorder",
			contenteditable: "contentEditable"
		},
		prop: function(e, t, n) {
			var r, s, a, o = e.nodeType;
			if (!e || o === 3 || o === 8 || o === 2) {
				return
			}
			a = o !== 1 || !v.isXMLDoc(e);
			if (a) {
				t = v.propFix[t] || t;
				s = v.propHooks[t]
			}
			if (n !== i) {
				if (s && "set" in s && (r = s.set(e, n, t)) !== i) {
					return r
				} else {
					return e[t] = n
				}
			} else {
				if (s && "get" in s && (r = s.get(e, t)) !== null) {
					return r
				} else {
					return e[t]
				}
			}
		},
		propHooks: {
			tabIndex: {
				get: function(e) {
					var t = e.getAttributeNode("tabindex");
					return t && t.specified ? parseInt(t.value, 10) : z.test(e.nodeName) || V.test(e.nodeName) && e.href ? 0 : i
				}
			}
		}
	});
	P = {
		get: function(e, t) {
			var n, r = v.prop(e, t);
			return r === true || typeof r !== "boolean" && (n = e.getAttributeNode(t)) && n.nodeValue !== false ? t.toLowerCase() : i
		},
		set: function(e, t, n) {
			var i;
			if (t === false) {
				v.removeAttr(e, n)
			} else {
				i = v.propFix[n] || n;
				if (i in e) {
					e[i] = true
				}
				e.setAttribute(n, n.toLowerCase())
			}
			return n
		}
	};
	if (!X) {
		O = {
			name: true,
			id: true,
			coords: true
		};
		H = v.valHooks.button = {
			get: function(e, t) {
				var n;
				n = e.getAttributeNode(t);
				return n && (O[t] ? n.value !== "" : n.specified) ? n.value : i
			},
			set: function(e, t, n) {
				var i = e.getAttributeNode(n);
				if (!i) {
					i = a.createAttribute(n);
					e.setAttributeNode(i)
				}
				return i.value = t + ""
			}
		};
		v.each(["width", "height"], function(e, t) {
			v.attrHooks[t] = v.extend(v.attrHooks[t], {
				set: function(e, n) {
					if (n === "") {
						e.setAttribute(t, "auto");
						return n
					}
				}
			})
		});
		v.attrHooks.contenteditable = {
			get: H.get,
			set: function(e, t, n) {
				if (t === "") {
					t = "false"
				}
				H.set(e, t, n)
			}
		}
	}
	if (!v.support.hrefNormalized) {
		v.each(["href", "src", "width", "height"], function(e, t) {
			v.attrHooks[t] = v.extend(v.attrHooks[t], {
				get: function(e) {
					var n = e.getAttribute(t, 2);
					return n === null ? i : n
				}
			})
		})
	}
	if (!v.support.style) {
		v.attrHooks.style = {
			get: function(e) {
				return e.style.cssText.toLowerCase() || i
			},
			set: function(e, t) {
				return e.style.cssText = t + ""
			}
		}
	}
	if (!v.support.optSelected) {
		v.propHooks.selected = v.extend(v.propHooks.selected, {
			get: function(e) {
				var t = e.parentNode;
				if (t) {
					t.selectedIndex;
					if (t.parentNode) {
						t.parentNode.selectedIndex
					}
				}
				return null
			}
		})
	}
	if (!v.support.enctype) {
		v.propFix.enctype = "encoding"
	}
	if (!v.support.checkOn) {
		v.each(["radio", "checkbox"], function() {
			v.valHooks[this] = {
				get: function(e) {
					return e.getAttribute("value") === null ? "on" : e.value
				}
			}
		})
	}
	v.each(["radio", "checkbox"], function() {
		v.valHooks[this] = v.extend(v.valHooks[this], {
			set: function(e, t) {
				if (v.isArray(t)) {
					return e.checked = v.inArray(v(e).val(), t) >= 0
				}
			}
		})
	});
	var Y = /^(?:textarea|input|select)$/i,
		Q = /^([^\.]*|)(?:\.(.+)|)$/,
		K = /(?:^|\s)hover(\.\S+|)\b/,
		J = /^key/,
		Z = /^(?:mouse|contextmenu)|click/,
		ee = /^(?:focusinfocus|focusoutblur)$/,
		te = function(e) {
			return v.event.special.hover ? e : e.replace(K, "mouseenter$1 mouseleave$1")
		};
	v.event = {
		add: function(e, t, n, r, s) {
			var a, o, l, c, u, f, d, p, h, m, g;
			if (e.nodeType === 3 || e.nodeType === 8 || !t || !n || !(a = v._data(e))) {
				return
			}
			if (n.handler) {
				h = n;
				n = h.handler;
				s = h.selector
			}
			if (!n.guid) {
				n.guid = v.guid++
			}
			l = a.events;
			if (!l) {
				a.events = l = {}
			}
			o = a.handle;
			if (!o) {
				a.handle = o = function(e) {
					return typeof v !== "undefined" && (!e || v.event.triggered !== e.type) ? v.event.dispatch.apply(o.elem, arguments) : i
				};
				o.elem = e
			}
			t = v.trim(te(t)).split(" ");
			for (c = 0; c < t.length; c++) {
				u = Q.exec(t[c]) || [];
				f = u[1];
				d = (u[2] || "").split(".").sort();
				g = v.event.special[f] || {};
				f = (s ? g.delegateType : g.bindType) || f;
				g = v.event.special[f] || {};
				p = v.extend({
					type: f,
					origType: u[1],
					data: r,
					handler: n,
					guid: n.guid,
					selector: s,
					needsContext: s && v.expr.match.needsContext.test(s),
					namespace: d.join(".")
				}, h);
				m = l[f];
				if (!m) {
					m = l[f] = [];
					m.delegateCount = 0;
					if (!g.setup || g.setup.call(e, r, d, o) === false) {
						if (e.addEventListener) {
							e.addEventListener(f, o, false)
						} else if (e.attachEvent) {
							e.attachEvent("on" + f, o)
						}
					}
				}
				if (g.add) {
					g.add.call(e, p);
					if (!p.handler.guid) {
						p.handler.guid = n.guid
					}
				}
				if (s) {
					m.splice(m.delegateCount++, 0, p)
				} else {
					m.push(p)
				}
				v.event.global[f] = true
			}
			e = null
		},
		global: {},
		remove: function(e, t, n, i, r) {
			var s, a, o, l, c, u, f, d, p, h, m, g = v.hasData(e) && v._data(e);
			if (!g || !(d = g.events)) {
				return
			}
			t = v.trim(te(t || "")).split(" ");
			for (s = 0; s < t.length; s++) {
				a = Q.exec(t[s]) || [];
				o = l = a[1];
				c = a[2];
				if (!o) {
					for (o in d) {
						v.event.remove(e, o + t[s], n, i, true)
					}
					continue
				}
				p = v.event.special[o] || {};
				o = (i ? p.delegateType : p.bindType) || o;
				h = d[o] || [];
				u = h.length;
				c = c ? new RegExp("(^|\\.)" + c.split(".").sort().join("\\.(?:.*\\.|)") + "(\\.|$)") : null;
				for (f = 0; f < h.length; f++) {
					m = h[f];
					if ((r || l === m.origType) && (!n || n.guid === m.guid) && (!c || c.test(m.namespace)) && (!i || i === m.selector || i === "**" && m.selector)) {
						h.splice(f--, 1);
						if (m.selector) {
							h.delegateCount--
						}
						if (p.remove) {
							p.remove.call(e, m)
						}
					}
				}
				if (h.length === 0 && u !== h.length) {
					if (!p.teardown || p.teardown.call(e, c, g.handle) === false) {
						v.removeEvent(e, o, g.handle)
					}
					delete d[o]
				}
			}
			if (v.isEmptyObject(d)) {
				delete g.handle;
				v.removeData(e, "events", true)
			}
		},
		customEvent: {
			getData: true,
			setData: true,
			changeData: true
		},
		trigger: function(e, t, n, r) {
			if (n && (n.nodeType === 3 || n.nodeType === 8)) {
				return
			}
			var s, o, l, c, u, f, d, p, h, m, g = e.type || e,
				y = [];
			if (ee.test(g + v.event.triggered)) {
				return
			}
			if (g.indexOf("!") >= 0) {
				g = g.slice(0, -1);
				o = true
			}
			if (g.indexOf(".") >= 0) {
				y = g.split(".");
				g = y.shift();
				y.sort()
			}
			if ((!n || v.event.customEvent[g]) && !v.event.global[g]) {
				return
			}
			e = typeof e === "object" ? e[v.expando] ? e : new v.Event(g, e) : new v.Event(g);
			e.type = g;
			e.isTrigger = true;
			e.exclusive = o;
			e.namespace = y.join(".");
			e.namespace_re = e.namespace ? new RegExp("(^|\\.)" + y.join("\\.(?:.*\\.|)") + "(\\.|$)") : null;
			f = g.indexOf(":") < 0 ? "on" + g : "";
			if (!n) {
				s = v.cache;
				for (l in s) {
					if (s[l].events && s[l].events[g]) {
						v.event.trigger(e, t, s[l].handle.elem, true)
					}
				}
				return
			}
			e.result = i;
			if (!e.target) {
				e.target = n
			}
			t = t != null ? v.makeArray(t) : [];
			t.unshift(e);
			d = v.event.special[g] || {};
			if (d.trigger && d.trigger.apply(n, t) === false) {
				return
			}
			h = [
				[n, d.bindType || g]
			];
			if (!r && !d.noBubble && !v.isWindow(n)) {
				m = d.delegateType || g;
				c = ee.test(m + g) ? n : n.parentNode;
				for (u = n; c; c = c.parentNode) {
					h.push([c, m]);
					u = c
				}
				if (u === (n.ownerDocument || a)) {
					h.push([u.defaultView || u.parentWindow || window, m])
				}
			}
			for (l = 0; l < h.length && !e.isPropagationStopped(); l++) {
				c = h[l][0];
				e.type = h[l][1];
				p = (v._data(c, "events") || {})[e.type] && v._data(c, "handle");
				if (p) {
					p.apply(c, t)
				}
				p = f && c[f];
				if (p && v.acceptData(c) && p.apply && p.apply(c, t) === false) {
					e.preventDefault()
				}
			}
			e.type = g;
			if (!r && !e.isDefaultPrevented()) {
				if ((!d._default || d._default.apply(n.ownerDocument, t) === false) && !(g === "click" && v.nodeName(n, "a")) && v.acceptData(n)) {
					if (f && n[g] && (g !== "focus" && g !== "blur" || e.target.offsetWidth !== 0) && !v.isWindow(n)) {
						u = n[f];
						if (u) {
							n[f] = null
						}
						v.event.triggered = g;
						n[g]();
						v.event.triggered = i;
						if (u) {
							n[f] = u
						}
					}
				}
			}
			return e.result
		},
		dispatch: function(e) {
			e = v.event.fix(e || window.event);
			var t, n, r, s, a, o, l, c, u, f, p = (v._data(this, "events") || {})[e.type] || [],
				h = p.delegateCount,
				m = d.call(arguments),
				g = !e.exclusive && !e.namespace,
				y = v.event.special[e.type] || {},
				b = [];
			m[0] = e;
			e.delegateTarget = this;
			if (y.preDispatch && y.preDispatch.call(this, e) === false) {
				return
			}
			if (h && !(e.button && e.type === "click")) {
				for (r = e.target; r != this; r = r.parentNode || this) {
					if (r.disabled !== true || e.type !== "click") {
						a = {};
						l = [];
						for (t = 0; t < h; t++) {
							c = p[t];
							u = c.selector;
							if (a[u] === i) {
								a[u] = c.needsContext ? v(u, this).index(r) >= 0 : v.find(u, this, null, [r]).length
							}
							if (a[u]) {
								l.push(c)
							}
						}
						if (l.length) {
							b.push({
								elem: r,
								matches: l
							})
						}
					}
				}
			}
			if (p.length > h) {
				b.push({
					elem: this,
					matches: p.slice(h)
				})
			}
			for (t = 0; t < b.length && !e.isPropagationStopped(); t++) {
				o = b[t];
				e.currentTarget = o.elem;
				for (n = 0; n < o.matches.length && !e.isImmediatePropagationStopped(); n++) {
					c = o.matches[n];
					if (g || !e.namespace && !c.namespace || e.namespace_re && e.namespace_re.test(c.namespace)) {
						e.data = c.data;
						e.handleObj = c;
						s = ((v.event.special[c.origType] || {}).handle || c.handler).apply(o.elem, m);
						if (s !== i) {
							e.result = s;
							if (s === false) {
								e.preventDefault();
								e.stopPropagation()
							}
						}
					}
				}
			}
			if (y.postDispatch) {
				y.postDispatch.call(this, e)
			}
			return e.result
		},
		props: "attrChange attrName relatedNode srcElement altKey bubbles cancelable ctrlKey currentTarget eventPhase metaKey relatedTarget shiftKey target timeStamp view which".split(" "),
		fixHooks: {},
		keyHooks: {
			props: "char charCode key keyCode".split(" "),
			filter: function(e, t) {
				if (e.which == null) {
					e.which = t.charCode != null ? t.charCode : t.keyCode
				}
				return e
			}
		},
		mouseHooks: {
			props: "button buttons clientX clientY fromElement offsetX offsetY pageX pageY screenX screenY toElement".split(" "),
			filter: function(e, t) {
				var n, r, s, o = t.button,
					l = t.fromElement;
				if (e.pageX == null && t.clientX != null) {
					n = e.target.ownerDocument || a;
					r = n.documentElement;
					s = n.body;
					e.pageX = t.clientX + (r && r.scrollLeft || s && s.scrollLeft || 0) - (r && r.clientLeft || s && s.clientLeft || 0);
					e.pageY = t.clientY + (r && r.scrollTop || s && s.scrollTop || 0) - (r && r.clientTop || s && s.clientTop || 0)
				}
				if (!e.relatedTarget && l) {
					e.relatedTarget = l === e.target ? t.toElement : l
				}
				if (!e.which && o !== i) {
					e.which = o & 1 ? 1 : o & 2 ? 3 : o & 4 ? 2 : 0
				}
				return e
			}
		},
		fix: function(e) {
			if (e[v.expando]) {
				return e
			}
			var t, n, i = e,
				r = v.event.fixHooks[e.type] || {},
				s = r.props ? this.props.concat(r.props) : this.props;
			e = v.Event(i);
			for (t = s.length; t;) {
				n = s[--t];
				e[n] = i[n]
			}
			if (!e.target) {
				e.target = i.srcElement || a
			}
			if (e.target.nodeType === 3) {
				e.target = e.target.parentNode
			}
			e.metaKey = !!e.metaKey;
			return r.filter ? r.filter(e, i) : e
		},
		special: {
			load: {
				noBubble: true
			},
			focus: {
				delegateType: "focusin"
			},
			blur: {
				delegateType: "focusout"
			},
			beforeunload: {
				setup: function(e, t, n) {
					if (v.isWindow(this)) {
						this.onbeforeunload = n
					}
				},
				teardown: function(e, t) {
					if (this.onbeforeunload === t) {
						this.onbeforeunload = null
					}
				}
			}
		},
		simulate: function(e, t, n, i) {
			var r = v.extend(new v.Event, n, {
				type: e,
				isSimulated: true,
				originalEvent: {}
			});
			if (i) {
				v.event.trigger(r, null, t)
			} else {
				v.event.dispatch.call(t, r)
			} if (r.isDefaultPrevented()) {
				n.preventDefault()
			}
		}
	};
	v.event.handle = v.event.dispatch;
	v.removeEvent = a.removeEventListener ? function(e, t, n) {
		if (e.removeEventListener) {
			e.removeEventListener(t, n, false)
		}
	} : function(e, t, n) {
		var i = "on" + t;
		if (e.detachEvent) {
			if (typeof e[i] === "undefined") {
				e[i] = null
			}
			e.detachEvent(i, n)
		}
	};
	v.Event = function(e, t) {
		if (!(this instanceof v.Event)) {
			return new v.Event(e, t)
		}
		if (e && e.type) {
			this.originalEvent = e;
			this.type = e.type;
			this.isDefaultPrevented = e.defaultPrevented || e.returnValue === false || e.getPreventDefault && e.getPreventDefault() ? ie : ne
		} else {
			this.type = e
		} if (t) {
			v.extend(this, t)
		}
		this.timeStamp = e && e.timeStamp || v.now();
		this[v.expando] = true
	};

	function ne() {
		return false
	}

	function ie() {
		return true
	}
	v.Event.prototype = {
		preventDefault: function() {
			this.isDefaultPrevented = ie;
			var e = this.originalEvent;
			if (!e) {
				return
			}
			if (e.preventDefault) {
				e.preventDefault()
			} else {
				e.returnValue = false
			}
		},
		stopPropagation: function() {
			this.isPropagationStopped = ie;
			var e = this.originalEvent;
			if (!e) {
				return
			}
			if (e.stopPropagation) {
				e.stopPropagation()
			}
			e.cancelBubble = true
		},
		stopImmediatePropagation: function() {
			this.isImmediatePropagationStopped = ie;
			this.stopPropagation()
		},
		isDefaultPrevented: ne,
		isPropagationStopped: ne,
		isImmediatePropagationStopped: ne
	};
	v.each({
		mouseenter: "mouseover",
		mouseleave: "mouseout"
	}, function(e, t) {
		v.event.special[e] = {
			delegateType: t,
			bindType: t,
			handle: function(e) {
				var n, i = this,
					r = e.relatedTarget,
					s = e.handleObj,
					a = s.selector;
				if (!r || r !== i && !v.contains(i, r)) {
					e.type = s.origType;
					n = s.handler.apply(this, arguments);
					e.type = t
				}
				return n
			}
		}
	});
	if (!v.support.submitBubbles) {
		v.event.special.submit = {
			setup: function() {
				if (v.nodeName(this, "form")) {
					return false
				}
				v.event.add(this, "click._submit keypress._submit", function(e) {
					var t = e.target,
						n = v.nodeName(t, "input") || v.nodeName(t, "button") ? t.form : i;
					if (n && !v._data(n, "_submit_attached")) {
						v.event.add(n, "submit._submit", function(e) {
							e._submit_bubble = true
						});
						v._data(n, "_submit_attached", true)
					}
				})
			},
			postDispatch: function(e) {
				if (e._submit_bubble) {
					delete e._submit_bubble;
					if (this.parentNode && !e.isTrigger) {
						v.event.simulate("submit", this.parentNode, e, true)
					}
				}
			},
			teardown: function() {
				if (v.nodeName(this, "form")) {
					return false
				}
				v.event.remove(this, "._submit")
			}
		}
	}
	if (!v.support.changeBubbles) {
		v.event.special.change = {
			setup: function() {
				if (Y.test(this.nodeName)) {
					if (this.type === "checkbox" || this.type === "radio") {
						v.event.add(this, "propertychange._change", function(e) {
							if (e.originalEvent.propertyName === "checked") {
								this._just_changed = true
							}
						});
						v.event.add(this, "click._change", function(e) {
							if (this._just_changed && !e.isTrigger) {
								this._just_changed = false
							}
							v.event.simulate("change", this, e, true)
						})
					}
					return false
				}
				v.event.add(this, "beforeactivate._change", function(e) {
					var t = e.target;
					if (Y.test(t.nodeName) && !v._data(t, "_change_attached")) {
						v.event.add(t, "change._change", function(e) {
							if (this.parentNode && !e.isSimulated && !e.isTrigger) {
								v.event.simulate("change", this.parentNode, e, true)
							}
						});
						v._data(t, "_change_attached", true)
					}
				})
			},
			handle: function(e) {
				var t = e.target;
				if (this !== t || e.isSimulated || e.isTrigger || t.type !== "radio" && t.type !== "checkbox") {
					return e.handleObj.handler.apply(this, arguments)
				}
			},
			teardown: function() {
				v.event.remove(this, "._change");
				return !Y.test(this.nodeName)
			}
		}
	}
	if (!v.support.focusinBubbles) {
		v.each({
			focus: "focusin",
			blur: "focusout"
		}, function(e, t) {
			var n = 0,
				i = function(e) {
					v.event.simulate(t, e.target, v.event.fix(e), true)
				};
			v.event.special[t] = {
				setup: function() {
					if (n++ === 0) {
						a.addEventListener(e, i, true)
					}
				},
				teardown: function() {
					if (--n === 0) {
						a.removeEventListener(e, i, true)
					}
				}
			}
		})
	}
	v.fn.extend({
		on: function(e, t, n, r, s) {
			var a, o;
			if (typeof e === "object") {
				if (typeof t !== "string") {
					n = n || t;
					t = i
				}
				for (o in e) {
					this.on(o, t, n, e[o], s)
				}
				return this
			}
			if (n == null && r == null) {
				r = t;
				n = t = i
			} else if (r == null) {
				if (typeof t === "string") {
					r = n;
					n = i
				} else {
					r = n;
					n = t;
					t = i
				}
			}
			if (r === false) {
				r = ne
			} else if (!r) {
				return this
			}
			if (s === 1) {
				a = r;
				r = function(e) {
					v().off(e);
					return a.apply(this, arguments)
				};
				r.guid = a.guid || (a.guid = v.guid++)
			}
			return this.each(function() {
				v.event.add(this, e, r, n, t)
			})
		},
		one: function(e, t, n, i) {
			return this.on(e, t, n, i, 1)
		},
		off: function(e, t, n) {
			var r, s;
			if (e && e.preventDefault && e.handleObj) {
				r = e.handleObj;
				v(e.delegateTarget).off(r.namespace ? r.origType + "." + r.namespace : r.origType, r.selector, r.handler);
				return this
			}
			if (typeof e === "object") {
				for (s in e) {
					this.off(s, t, e[s])
				}
				return this
			}
			if (t === false || typeof t === "function") {
				n = t;
				t = i
			}
			if (n === false) {
				n = ne
			}
			return this.each(function() {
				v.event.remove(this, e, n, t)
			})
		},
		bind: function(e, t, n) {
			return this.on(e, null, t, n)
		},
		unbind: function(e, t) {
			return this.off(e, null, t)
		},
		live: function(e, t, n) {
			v(this.context).on(e, this.selector, t, n);
			return this
		},
		die: function(e, t) {
			v(this.context).off(e, this.selector || "**", t);
			return this
		},
		delegate: function(e, t, n, i) {
			return this.on(t, e, n, i)
		},
		undelegate: function(e, t, n) {
			return arguments.length === 1 ? this.off(e, "**") : this.off(t, e || "**", n)
		},
		trigger: function(e, t) {
			return this.each(function() {
				v.event.trigger(e, t, this)
			})
		},
		triggerHandler: function(e, t) {
			if (this[0]) {
				return v.event.trigger(e, t, this[0], true)
			}
		},
		toggle: function(e) {
			var t = arguments,
				n = e.guid || v.guid++,
				i = 0,
				r = function(n) {
					var r = (v._data(this, "lastToggle" + e.guid) || 0) % i;
					v._data(this, "lastToggle" + e.guid, r + 1);
					n.preventDefault();
					return t[r].apply(this, arguments) || false
				};
			r.guid = n;
			while (i < t.length) {
				t[i++].guid = n
			}
			return this.click(r)
		},
		hover: function(e, t) {
			return this.mouseenter(e).mouseleave(t || e)
		}
	});
	v.each(("blur focus focusin focusout load resize scroll unload click dblclick " + "mousedown mouseup mousemove mouseover mouseout mouseenter mouseleave " + "change select submit keydown keypress keyup error contextmenu").split(" "), function(e, t) {
		v.fn[t] = function(e, n) {
			if (n == null) {
				n = e;
				e = null
			}
			return arguments.length > 0 ? this.on(t, null, e, n) : this.trigger(t)
		};
		if (J.test(t)) {
			v.event.fixHooks[t] = v.event.keyHooks
		}
		if (Z.test(t)) {
			v.event.fixHooks[t] = v.event.mouseHooks
		}
	});
	(function(e, t) {
		var n, i, r, s, a, o, l, c, u, f, d = true,
			p = "undefined",
			h = ("sizcache" + Math.random()).replace(".", ""),
			m = String,
			g = e.document,
			y = g.documentElement,
			b = 0,
			w = 0,
			x = [].pop,
			T = [].push,
			k = [].slice,
			C = [].indexOf || function(e) {
				var t = 0,
					n = this.length;
				for (; t < n; t++) {
					if (this[t] === e) {
						return t
					}
				}
				return -1
			},
			j = function(e, t) {
				e[h] = t == null || t;
				return e
			},
			E = function() {
				var e = {},
					t = [];
				return j(function(n, i) {
					if (t.push(n) > r.cacheLength) {
						delete e[t.shift()]
					}
					return e[n + " "] = i
				}, e)
			},
			S = E(),
			N = E(),
			L = E(),
			I = "[\\x20\\t\\r\\n\\f]",
			M = "(?:\\\\.|[-\\w]|[^\\x00-\\xa0])+",
			F = M.replace("w", "w#"),
			_ = "([*^$|!~]?=)",
			A = "\\[" + I + "*(" + M + ")" + I + "*(?:" + _ + I + "*(?:(['\"])((?:\\\\.|[^\\\\])*?)\\3|(" + F + ")|)|)" + I + "*\\]",
			$ = ":(" + M + ")(?:\\((?:(['\"])((?:\\\\.|[^\\\\])*?)\\2|([^()[\\]]*|(?:(?:" + A + ")|[^:]|\\\\.)*|.*))\\)|)",
			B = ":(even|odd|eq|gt|lt|nth|first|last)(?:\\(" + I + "*((?:-\\d)?\\d*)" + I + "*\\)|)(?=[^-]|$)",
			D = new RegExp("^" + I + "+|((?:^|[^\\\\])(?:\\\\.)*)" + I + "+$", "g"),
			R = new RegExp("^" + I + "*," + I + "*"),
			H = new RegExp("^" + I + "*([\\x20\\t\\r\\n\\f>+~])" + I + "*"),
			P = new RegExp($),
			O = /^(?:#([\w\-]+)|(\w+)|\.([\w\-]+))$/,
			W = /^:not/,
			q = /[\x20\t\r\n\f]*[+~]/,
			U = /:not\($/,
			z = /h\d/i,
			V = /input|select|textarea|button/i,
			G = /\\(?!\\)/g,
			X = {
				ID: new RegExp("^#(" + M + ")"),
				CLASS: new RegExp("^\\.(" + M + ")"),
				NAME: new RegExp("^\\[name=['\"]?(" + M + ")['\"]?\\]"),
				TAG: new RegExp("^(" + M.replace("w", "w*") + ")"),
				ATTR: new RegExp("^" + A),
				PSEUDO: new RegExp("^" + $),
				POS: new RegExp(B, "i"),
				CHILD: new RegExp("^:(only|nth|first|last)-child(?:\\(" + I + "*(even|odd|(([+-]|)(\\d*)n|)" + I + "*(?:([+-]|)" + I + "*(\\d+)|))" + I + "*\\)|)", "i"),
				needsContext: new RegExp("^" + I + "*[>+~]|" + B, "i")
			},
			Y = function(e) {
				var t = g.createElement("div");
				try {
					return e(t)
				} catch (n) {
					return false
				} finally {
					t = null
				}
			},
			Q = Y(function(e) {
				e.appendChild(g.createComment(""));
				return !e.getElementsByTagName("*").length
			}),
			K = Y(function(e) {
				e.innerHTML = "<a href='#'></a>";
				return e.firstChild && typeof e.firstChild.getAttribute !== p && e.firstChild.getAttribute("href") === "#"
			}),
			J = Y(function(e) {
				e.innerHTML = "<select></select>";
				var t = typeof e.lastChild.getAttribute("multiple");
				return t !== "boolean" && t !== "string"
			}),
			Z = Y(function(e) {
				e.innerHTML = "<div class='hidden e'></div><div class='hidden'></div>";
				if (!e.getElementsByClassName || !e.getElementsByClassName("e").length) {
					return false
				}
				e.lastChild.className = "e";
				return e.getElementsByClassName("e").length === 2
			}),
			ee = Y(function(e) {
				e.id = h + 0;
				e.innerHTML = "<a name='" + h + "'></a><div name='" + h + "'></div>";
				y.insertBefore(e, y.firstChild);
				var t = g.getElementsByName && g.getElementsByName(h).length === 2 + g.getElementsByName(h + 0).length;
				i = !g.getElementById(h);
				y.removeChild(e);
				return t
			});
		try {
			k.call(y.childNodes, 0)[0].nodeType
		} catch (te) {
			k = function(e) {
				var t, n = [];
				for (; t = this[e]; e++) {
					n.push(t)
				}
				return n
			}
		}

		function ne(e, t, n, i) {
			n = n || [];
			t = t || g;
			var r, s, l, c, u = t.nodeType;
			if (!e || typeof e !== "string") {
				return n
			}
			if (u !== 1 && u !== 9) {
				return []
			}
			l = a(t);
			if (!l && !i) {
				if (r = O.exec(e)) {
					if (c = r[1]) {
						if (u === 9) {
							s = t.getElementById(c);
							if (s && s.parentNode) {
								if (s.id === c) {
									n.push(s);
									return n
								}
							} else {
								return n
							}
						} else {
							if (t.ownerDocument && (s = t.ownerDocument.getElementById(c)) && o(t, s) && s.id === c) {
								n.push(s);
								return n
							}
						}
					} else if (r[2]) {
						T.apply(n, k.call(t.getElementsByTagName(e), 0));
						return n
					} else if ((c = r[3]) && Z && t.getElementsByClassName) {
						T.apply(n, k.call(t.getElementsByClassName(c), 0));
						return n
					}
				}
			}
			return me(e.replace(D, "$1"), t, n, i, l)
		}
		ne.matches = function(e, t) {
			return ne(e, null, null, t)
		};
		ne.matchesSelector = function(e, t) {
			return ne(t, null, null, [e]).length > 0
		};

		function ie(e) {
			return function(t) {
				var n = t.nodeName.toLowerCase();
				return n === "input" && t.type === e
			}
		}

		function re(e) {
			return function(t) {
				var n = t.nodeName.toLowerCase();
				return (n === "input" || n === "button") && t.type === e
			}
		}

		function se(e) {
			return j(function(t) {
				t = +t;
				return j(function(n, i) {
					var r, s = e([], n.length, t),
						a = s.length;
					while (a--) {
						if (n[r = s[a]]) {
							n[r] = !(i[r] = n[r])
						}
					}
				})
			})
		}
		s = ne.getText = function(e) {
			var t, n = "",
				i = 0,
				r = e.nodeType;
			if (r) {
				if (r === 1 || r === 9 || r === 11) {
					if (typeof e.textContent === "string") {
						return e.textContent
					} else {
						for (e = e.firstChild; e; e = e.nextSibling) {
							n += s(e)
						}
					}
				} else if (r === 3 || r === 4) {
					return e.nodeValue
				}
			} else {
				for (; t = e[i]; i++) {
					n += s(t)
				}
			}
			return n
		};
		a = ne.isXML = function(e) {
			var t = e && (e.ownerDocument || e).documentElement;
			return t ? t.nodeName !== "HTML" : false
		};
		o = ne.contains = y.contains ? function(e, t) {
			var n = e.nodeType === 9 ? e.documentElement : e,
				i = t && t.parentNode;
			return e === i || !!(i && i.nodeType === 1 && n.contains && n.contains(i))
		} : y.compareDocumentPosition ? function(e, t) {
			return t && !!(e.compareDocumentPosition(t) & 16)
		} : function(e, t) {
			while (t = t.parentNode) {
				if (t === e) {
					return true
				}
			}
			return false
		};
		ne.attr = function(e, t) {
			var n, i = a(e);
			if (!i) {
				t = t.toLowerCase()
			}
			if (n = r.attrHandle[t]) {
				return n(e)
			}
			if (i || J) {
				return e.getAttribute(t)
			}
			n = e.getAttributeNode(t);
			return n ? typeof e[t] === "boolean" ? e[t] ? t : null : n.specified ? n.value : null : null
		};
		r = ne.selectors = {
			cacheLength: 50,
			createPseudo: j,
			match: X,
			attrHandle: K ? {} : {
				href: function(e) {
					return e.getAttribute("href", 2)
				},
				type: function(e) {
					return e.getAttribute("type")
				}
			},
			find: {
				ID: i ? function(e, t, n) {
					if (typeof t.getElementById !== p && !n) {
						var i = t.getElementById(e);
						return i && i.parentNode ? [i] : []
					}
				} : function(e, n, i) {
					if (typeof n.getElementById !== p && !i) {
						var r = n.getElementById(e);
						return r ? r.id === e || typeof r.getAttributeNode !== p && r.getAttributeNode("id").value === e ? [r] : t : []
					}
				},
				TAG: Q ? function(e, t) {
					if (typeof t.getElementsByTagName !== p) {
						return t.getElementsByTagName(e)
					}
				} : function(e, t) {
					var n = t.getElementsByTagName(e);
					if (e === "*") {
						var i, r = [],
							s = 0;
						for (; i = n[s]; s++) {
							if (i.nodeType === 1) {
								r.push(i)
							}
						}
						return r
					}
					return n
				},
				NAME: ee && function(e, t) {
					if (typeof t.getElementsByName !== p) {
						return t.getElementsByName(name)
					}
				},
				CLASS: Z && function(e, t, n) {
					if (typeof t.getElementsByClassName !== p && !n) {
						return t.getElementsByClassName(e)
					}
				}
			},
			relative: {
				">": {
					dir: "parentNode",
					first: true
				},
				" ": {
					dir: "parentNode"
				},
				"+": {
					dir: "previousSibling",
					first: true
				},
				"~": {
					dir: "previousSibling"
				}
			},
			preFilter: {
				ATTR: function(e) {
					e[1] = e[1].replace(G, "");
					e[3] = (e[4] || e[5] || "").replace(G, "");
					if (e[2] === "~=") {
						e[3] = " " + e[3] + " "
					}
					return e.slice(0, 4)
				},
				CHILD: function(e) {
					e[1] = e[1].toLowerCase();
					if (e[1] === "nth") {
						if (!e[2]) {
							ne.error(e[0])
						}
						e[3] = +(e[3] ? e[4] + (e[5] || 1) : 2 * (e[2] === "even" || e[2] === "odd"));
						e[4] = +(e[6] + e[7] || e[2] === "odd")
					} else if (e[2]) {
						ne.error(e[0])
					}
					return e
				},
				PSEUDO: function(e) {
					var t, n;
					if (X["CHILD"].test(e[0])) {
						return null
					}
					if (e[3]) {
						e[2] = e[3]
					} else if (t = e[4]) {
						if (P.test(t) && (n = oe(t, true)) && (n = t.indexOf(")", t.length - n) - t.length)) {
							t = t.slice(0, n);
							e[0] = e[0].slice(0, n)
						}
						e[2] = t
					}
					return e.slice(0, 3)
				}
			},
			filter: {
				ID: i ? function(e) {
					e = e.replace(G, "");
					return function(t) {
						return t.getAttribute("id") === e
					}
				} : function(e) {
					e = e.replace(G, "");
					return function(t) {
						var n = typeof t.getAttributeNode !== p && t.getAttributeNode("id");
						return n && n.value === e
					}
				},
				TAG: function(e) {
					if (e === "*") {
						return function() {
							return true
						}
					}
					e = e.replace(G, "").toLowerCase();
					return function(t) {
						return t.nodeName && t.nodeName.toLowerCase() === e
					}
				},
				CLASS: function(e) {
					var t = S[h][e + " "];
					return t || (t = new RegExp("(^|" + I + ")" + e + "(" + I + "|$)")) && S(e, function(e) {
						return t.test(e.className || typeof e.getAttribute !== p && e.getAttribute("class") || "")
					})
				},
				ATTR: function(e, t, n) {
					return function(i, r) {
						var s = ne.attr(i, e);
						if (s == null) {
							return t === "!="
						}
						if (!t) {
							return true
						}
						s += "";
						return t === "=" ? s === n : t === "!=" ? s !== n : t === "^=" ? n && s.indexOf(n) === 0 : t === "*=" ? n && s.indexOf(n) > -1 : t === "$=" ? n && s.substr(s.length - n.length) === n : t === "~=" ? (" " + s + " ").indexOf(n) > -1 : t === "|=" ? s === n || s.substr(0, n.length + 1) === n + "-" : false
					}
				},
				CHILD: function(e, t, n, i) {
					if (e === "nth") {
						return function(e) {
							var t, r, s = e.parentNode;
							if (n === 1 && i === 0) {
								return true
							}
							if (s) {
								r = 0;
								for (t = s.firstChild; t; t = t.nextSibling) {
									if (t.nodeType === 1) {
										r++;
										if (e === t) {
											break
										}
									}
								}
							}
							r -= i;
							return r === n || r % n === 0 && r / n >= 0
						}
					}
					return function(t) {
						var n = t;
						switch (e) {
							case "only":
							case "first":
								while (n = n.previousSibling) {
									if (n.nodeType === 1) {
										return false
									}
								}
								if (e === "first") {
									return true
								}
								n = t;
							case "last":
								while (n = n.nextSibling) {
									if (n.nodeType === 1) {
										return false
									}
								}
								return true
						}
					}
				},
				PSEUDO: function(e, t) {
					var n, i = r.pseudos[e] || r.setFilters[e.toLowerCase()] || ne.error("unsupported pseudo: " + e);
					if (i[h]) {
						return i(t)
					}
					if (i.length > 1) {
						n = [e, e, "", t];
						return r.setFilters.hasOwnProperty(e.toLowerCase()) ? j(function(e, n) {
							var r, s = i(e, t),
								a = s.length;
							while (a--) {
								r = C.call(e, s[a]);
								e[r] = !(n[r] = s[a])
							}
						}) : function(e) {
							return i(e, 0, n)
						}
					}
					return i
				}
			},
			pseudos: {
				not: j(function(e) {
					var t = [],
						n = [],
						i = l(e.replace(D, "$1"));
					return i[h] ? j(function(e, t, n, r) {
						var s, a = i(e, null, r, []),
							o = e.length;
						while (o--) {
							if (s = a[o]) {
								e[o] = !(t[o] = s)
							}
						}
					}) : function(e, r, s) {
						t[0] = e;
						i(t, null, s, n);
						return !n.pop()
					}
				}),
				has: j(function(e) {
					return function(t) {
						return ne(e, t).length > 0
					}
				}),
				contains: j(function(e) {
					return function(t) {
						return (t.textContent || t.innerText || s(t)).indexOf(e) > -1
					}
				}),
				enabled: function(e) {
					return e.disabled === false
				},
				disabled: function(e) {
					return e.disabled === true
				},
				checked: function(e) {
					var t = e.nodeName.toLowerCase();
					return t === "input" && !!e.checked || t === "option" && !!e.selected
				},
				selected: function(e) {
					if (e.parentNode) {
						e.parentNode.selectedIndex
					}
					return e.selected === true
				},
				parent: function(e) {
					return !r.pseudos["empty"](e)
				},
				empty: function(e) {
					var t;
					e = e.firstChild;
					while (e) {
						if (e.nodeName > "@" || (t = e.nodeType) === 3 || t === 4) {
							return false
						}
						e = e.nextSibling
					}
					return true
				},
				header: function(e) {
					return z.test(e.nodeName)
				},
				text: function(e) {
					var t, n;
					return e.nodeName.toLowerCase() === "input" && (t = e.type) === "text" && ((n = e.getAttribute("type")) == null || n.toLowerCase() === t)
				},
				radio: ie("radio"),
				checkbox: ie("checkbox"),
				file: ie("file"),
				password: ie("password"),
				image: ie("image"),
				submit: re("submit"),
				reset: re("reset"),
				button: function(e) {
					var t = e.nodeName.toLowerCase();
					return t === "input" && e.type === "button" || t === "button"
				},
				input: function(e) {
					return V.test(e.nodeName)
				},
				focus: function(e) {
					var t = e.ownerDocument;
					return e === t.activeElement && (!t.hasFocus || t.hasFocus()) && !!(e.type || e.href || ~e.tabIndex)
				},
				active: function(e) {
					return e === e.ownerDocument.activeElement
				},
				first: se(function() {
					return [0]
				}),
				last: se(function(e, t) {
					return [t - 1]
				}),
				eq: se(function(e, t, n) {
					return [n < 0 ? n + t : n]
				}),
				even: se(function(e, t) {
					for (var n = 0; n < t; n += 2) {
						e.push(n)
					}
					return e
				}),
				odd: se(function(e, t) {
					for (var n = 1; n < t; n += 2) {
						e.push(n)
					}
					return e
				}),
				lt: se(function(e, t, n) {
					for (var i = n < 0 ? n + t : n; --i >= 0;) {
						e.push(i)
					}
					return e
				}),
				gt: se(function(e, t, n) {
					for (var i = n < 0 ? n + t : n; ++i < t;) {
						e.push(i)
					}
					return e
				})
			}
		};

		function ae(e, t, n) {
			if (e === t) {
				return n
			}
			var i = e.nextSibling;
			while (i) {
				if (i === t) {
					return -1
				}
				i = i.nextSibling
			}
			return 1
		}
		c = y.compareDocumentPosition ? function(e, t) {
			if (e === t) {
				u = true;
				return 0
			}
			return (!e.compareDocumentPosition || !t.compareDocumentPosition ? e.compareDocumentPosition : e.compareDocumentPosition(t) & 4) ? -1 : 1
		} : function(e, t) {
			if (e === t) {
				u = true;
				return 0
			} else if (e.sourceIndex && t.sourceIndex) {
				return e.sourceIndex - t.sourceIndex
			}
			var n, i, r = [],
				s = [],
				a = e.parentNode,
				o = t.parentNode,
				l = a;
			if (a === o) {
				return ae(e, t)
			} else if (!a) {
				return -1
			} else if (!o) {
				return 1
			}
			while (l) {
				r.unshift(l);
				l = l.parentNode
			}
			l = o;
			while (l) {
				s.unshift(l);
				l = l.parentNode
			}
			n = r.length;
			i = s.length;
			for (var c = 0; c < n && c < i; c++) {
				if (r[c] !== s[c]) {
					return ae(r[c], s[c])
				}
			}
			return c === n ? ae(e, s[c], -1) : ae(r[c], t, 1)
		};
		[0, 0].sort(c);
		d = !u;
		ne.uniqueSort = function(e) {
			var t, n = [],
				i = 1,
				r = 0;
			u = d;
			e.sort(c);
			if (u) {
				for (; t = e[i]; i++) {
					if (t === e[i - 1]) {
						r = n.push(i)
					}
				}
				while (r--) {
					e.splice(n[r], 1)
				}
			}
			return e
		};
		ne.error = function(e) {
			throw new Error("Syntax error, unrecognized expression: " + e)
		};

		function oe(e, t) {
			var n, i, s, a, o, l, c, u = N[h][e + " "];
			if (u) {
				return t ? 0 : u.slice(0)
			}
			o = e;
			l = [];
			c = r.preFilter;
			while (o) {
				if (!n || (i = R.exec(o))) {
					if (i) {
						o = o.slice(i[0].length) || o
					}
					l.push(s = [])
				}
				n = false;
				if (i = H.exec(o)) {
					s.push(n = new m(i.shift()));
					o = o.slice(n.length);
					n.type = i[0].replace(D, " ")
				}
				for (a in r.filter) {
					if ((i = X[a].exec(o)) && (!c[a] || (i = c[a](i)))) {
						s.push(n = new m(i.shift()));
						o = o.slice(n.length);
						n.type = a;
						n.matches = i
					}
				}
				if (!n) {
					break
				}
			}
			return t ? o.length : o ? ne.error(e) : N(e, l).slice(0)
		}

		function le(e, t, i) {
			var r = t.dir,
				s = i && t.dir === "parentNode",
				a = w++;
			return t.first ? function(t, n, i) {
				while (t = t[r]) {
					if (s || t.nodeType === 1) {
						return e(t, n, i)
					}
				}
			} : function(t, i, o) {
				if (!o) {
					var l, c = b + " " + a + " ",
						u = c + n;
					while (t = t[r]) {
						if (s || t.nodeType === 1) {
							if ((l = t[h]) === u) {
								return t.sizset
							} else if (typeof l === "string" && l.indexOf(c) === 0) {
								if (t.sizset) {
									return t
								}
							} else {
								t[h] = u;
								if (e(t, i, o)) {
									t.sizset = true;
									return t
								}
								t.sizset = false
							}
						}
					}
				} else {
					while (t = t[r]) {
						if (s || t.nodeType === 1) {
							if (e(t, i, o)) {
								return t
							}
						}
					}
				}
			}
		}

		function ce(e) {
			return e.length > 1 ? function(t, n, i) {
				var r = e.length;
				while (r--) {
					if (!e[r](t, n, i)) {
						return false
					}
				}
				return true
			} : e[0]
		}

		function ue(e, t, n, i, r) {
			var s, a = [],
				o = 0,
				l = e.length,
				c = t != null;
			for (; o < l; o++) {
				if (s = e[o]) {
					if (!n || n(s, i, r)) {
						a.push(s);
						if (c) {
							t.push(o)
						}
					}
				}
			}
			return a
		}

		function fe(e, t, n, i, r, s) {
			if (i && !i[h]) {
				i = fe(i)
			}
			if (r && !r[h]) {
				r = fe(r, s)
			}
			return j(function(s, a, o, l) {
				var c, u, f, d = [],
					p = [],
					h = a.length,
					m = s || he(t || "*", o.nodeType ? [o] : o, []),
					g = e && (s || !t) ? ue(m, d, e, o, l) : m,
					v = n ? r || (s ? e : h || i) ? [] : a : g;
				if (n) {
					n(g, v, o, l)
				}
				if (i) {
					c = ue(v, p);
					i(c, [], o, l);
					u = c.length;
					while (u--) {
						if (f = c[u]) {
							v[p[u]] = !(g[p[u]] = f)
						}
					}
				}
				if (s) {
					if (r || e) {
						if (r) {
							c = [];
							u = v.length;
							while (u--) {
								if (f = v[u]) {
									c.push(g[u] = f)
								}
							}
							r(null, v = [], c, l)
						}
						u = v.length;
						while (u--) {
							if ((f = v[u]) && (c = r ? C.call(s, f) : d[u]) > -1) {
								s[c] = !(a[c] = f)
							}
						}
					}
				} else {
					v = ue(v === a ? v.splice(h, v.length) : v);
					if (r) {
						r(null, a, v, l)
					} else {
						T.apply(a, v)
					}
				}
			})
		}

		function de(e) {
			var t, n, i, s = e.length,
				a = r.relative[e[0].type],
				o = a || r.relative[" "],
				l = a ? 1 : 0,
				c = le(function(e) {
					return e === t
				}, o, true),
				u = le(function(e) {
					return C.call(t, e) > -1
				}, o, true),
				d = [
					function(e, n, i) {
						return !a && (i || n !== f) || ((t = n).nodeType ? c(e, n, i) : u(e, n, i))
					}
				];
			for (; l < s; l++) {
				if (n = r.relative[e[l].type]) {
					d = [le(ce(d), n)]
				} else {
					n = r.filter[e[l].type].apply(null, e[l].matches);
					if (n[h]) {
						i = ++l;
						for (; i < s; i++) {
							if (r.relative[e[i].type]) {
								break
							}
						}
						return fe(l > 1 && ce(d), l > 1 && e.slice(0, l - 1).join("").replace(D, "$1"), n, l < i && de(e.slice(l, i)), i < s && de(e = e.slice(i)), i < s && e.join(""))
					}
					d.push(n)
				}
			}
			return ce(d)
		}

		function pe(e, t) {
			var i = t.length > 0,
				s = e.length > 0,
				a = function(o, l, c, u, d) {
					var p, h, m, v = [],
						y = 0,
						w = "0",
						k = o && [],
						C = d != null,
						j = f,
						E = o || s && r.find["TAG"]("*", d && l.parentNode || l),
						S = b += j == null ? 1 : Math.E;
					if (C) {
						f = l !== g && l;
						n = a.el
					}
					for (;
						(p = E[w]) != null; w++) {
						if (s && p) {
							for (h = 0; m = e[h]; h++) {
								if (m(p, l, c)) {
									u.push(p);
									break
								}
							}
							if (C) {
								b = S;
								n = ++a.el
							}
						}
						if (i) {
							if (p = !m && p) {
								y--
							}
							if (o) {
								k.push(p)
							}
						}
					}
					y += w;
					if (i && w !== y) {
						for (h = 0; m = t[h]; h++) {
							m(k, v, l, c)
						}
						if (o) {
							if (y > 0) {
								while (w--) {
									if (!(k[w] || v[w])) {
										v[w] = x.call(u)
									}
								}
							}
							v = ue(v)
						}
						T.apply(u, v);
						if (C && !o && v.length > 0 && y + t.length > 1) {
							ne.uniqueSort(u)
						}
					}
					if (C) {
						b = S;
						f = j
					}
					return k
				};
			a.el = 0;
			return i ? j(a) : a
		}
		l = ne.compile = function(e, t) {
			var n, i = [],
				r = [],
				s = L[h][e + " "];
			if (!s) {
				if (!t) {
					t = oe(e)
				}
				n = t.length;
				while (n--) {
					s = de(t[n]);
					if (s[h]) {
						i.push(s)
					} else {
						r.push(s)
					}
				}
				s = L(e, pe(r, i))
			}
			return s
		};

		function he(e, t, n) {
			var i = 0,
				r = t.length;
			for (; i < r; i++) {
				ne(e, t[i], n)
			}
			return n
		}

		function me(e, t, n, i, s) {
			var a, o, c, u, f, d = oe(e),
				p = d.length;
			if (!i) {
				if (d.length === 1) {
					o = d[0] = d[0].slice(0);
					if (o.length > 2 && (c = o[0]).type === "ID" && t.nodeType === 9 && !s && r.relative[o[1].type]) {
						t = r.find["ID"](c.matches[0].replace(G, ""), t, s)[0];
						if (!t) {
							return n
						}
						e = e.slice(o.shift().length)
					}
					for (a = X["POS"].test(e) ? -1 : o.length - 1; a >= 0; a--) {
						c = o[a];
						if (r.relative[u = c.type]) {
							break
						}
						if (f = r.find[u]) {
							if (i = f(c.matches[0].replace(G, ""), q.test(o[0].type) && t.parentNode || t, s)) {
								o.splice(a, 1);
								e = i.length && o.join("");
								if (!e) {
									T.apply(n, k.call(i, 0));
									return n
								}
								break
							}
						}
					}
				}
			}
			l(e, d)(i, t, s, n, q.test(e));
			return n
		}
		if (g.querySelectorAll) {
			(function() {
				var e, t = me,
					n = /'|\\/g,
					i = /\=[\x20\t\r\n\f]*([^'"\]]*)[\x20\t\r\n\f]*\]/g,
					r = [":focus"],
					s = [":active"],
					o = y.matchesSelector || y.mozMatchesSelector || y.webkitMatchesSelector || y.oMatchesSelector || y.msMatchesSelector;
				Y(function(e) {
					e.innerHTML = "<select><option selected=''></option></select>";
					if (!e.querySelectorAll("[selected]").length) {
						r.push("\\[" + I + "*(?:checked|disabled|ismap|multiple|readonly|selected|value)")
					}
					if (!e.querySelectorAll(":checked").length) {
						r.push(":checked")
					}
				});
				Y(function(e) {
					e.innerHTML = "<p test=''></p>";
					if (e.querySelectorAll("[test^='']").length) {
						r.push("[*^$]=" + I + "*(?:\"\"|'')")
					}
					e.innerHTML = "<input type='hidden'/>";
					if (!e.querySelectorAll(":enabled").length) {
						r.push(":enabled", ":disabled")
					}
				});
				r = new RegExp(r.join("|"));
				me = function(e, i, s, a, o) {
					if (!a && !o && !r.test(e)) {
						var l, c, u = true,
							f = h,
							d = i,
							p = i.nodeType === 9 && e;
						if (i.nodeType === 1 && i.nodeName.toLowerCase() !== "object") {
							l = oe(e);
							if (u = i.getAttribute("id")) {
								f = u.replace(n, "\\$&")
							} else {
								i.setAttribute("id", f)
							}
							f = "[id='" + f + "'] ";
							c = l.length;
							while (c--) {
								l[c] = f + l[c].join("")
							}
							d = q.test(e) && i.parentNode || i;
							p = l.join(",")
						}
						if (p) {
							try {
								T.apply(s, k.call(d.querySelectorAll(p), 0));
								return s
							} catch (m) {} finally {
								if (!u) {
									i.removeAttribute("id")
								}
							}
						}
					}
					return t(e, i, s, a, o)
				};
				if (o) {
					Y(function(t) {
						e = o.call(t, "div");
						try {
							o.call(t, "[test!='']:sizzle");
							s.push("!=", $)
						} catch (n) {}
					});
					s = new RegExp(s.join("|"));
					ne.matchesSelector = function(t, n) {
						n = n.replace(i, "='$1']");
						if (!a(t) && !s.test(n) && !r.test(n)) {
							try {
								var l = o.call(t, n);
								if (l || e || t.document && t.document.nodeType !== 11) {
									return l
								}
							} catch (c) {}
						}
						return ne(n, null, null, [t]).length > 0
					}
				}
			})()
		}
		r.pseudos["nth"] = r.pseudos["eq"];

		function ge() {}
		r.filters = ge.prototype = r.pseudos;
		r.setFilters = new ge;
		ne.attr = v.attr;
		v.find = ne;
		v.expr = ne.selectors;
		v.expr[":"] = v.expr.pseudos;
		v.unique = ne.uniqueSort;
		v.text = ne.getText;
		v.isXMLDoc = ne.isXML;
		v.contains = ne.contains
	})(window);
	var re = /Until$/,
		se = /^(?:parents|prev(?:Until|All))/,
		ae = /^.[^:#\[\.,]*$/,
		oe = v.expr.match.needsContext,
		le = {
			children: true,
			contents: true,
			next: true,
			prev: true
		};
	v.fn.extend({
		find: function(e) {
			var t, n, i, r, s, a, o = this;
			if (typeof e !== "string") {
				return v(e).filter(function() {
					for (t = 0, n = o.length; t < n; t++) {
						if (v.contains(o[t], this)) {
							return true
						}
					}
				})
			}
			a = this.pushStack("", "find", e);
			for (t = 0, n = this.length; t < n; t++) {
				i = a.length;
				v.find(e, this[t], a);
				if (t > 0) {
					for (r = i; r < a.length; r++) {
						for (s = 0; s < i; s++) {
							if (a[s] === a[r]) {
								a.splice(r--, 1);
								break
							}
						}
					}
				}
			}
			return a
		},
		has: function(e) {
			var t, n = v(e, this),
				i = n.length;
			return this.filter(function() {
				for (t = 0; t < i; t++) {
					if (v.contains(this, n[t])) {
						return true
					}
				}
			})
		},
		not: function(e) {
			return this.pushStack(fe(this, e, false), "not", e)
		},
		filter: function(e) {
			return this.pushStack(fe(this, e, true), "filter", e)
		},
		is: function(e) {
			return !!e && (typeof e === "string" ? oe.test(e) ? v(e, this.context).index(this[0]) >= 0 : v.filter(e, this).length > 0 : this.filter(e).length > 0)
		},
		closest: function(e, t) {
			var n, i = 0,
				r = this.length,
				s = [],
				a = oe.test(e) || typeof e !== "string" ? v(e, t || this.context) : 0;
			for (; i < r; i++) {
				n = this[i];
				while (n && n.ownerDocument && n !== t && n.nodeType !== 11) {
					if (a ? a.index(n) > -1 : v.find.matchesSelector(n, e)) {
						s.push(n);
						break
					}
					n = n.parentNode
				}
			}
			s = s.length > 1 ? v.unique(s) : s;
			return this.pushStack(s, "closest", e)
		},
		index: function(e) {
			if (!e) {
				return this[0] && this[0].parentNode ? this.prevAll().length : -1
			}
			if (typeof e === "string") {
				return v.inArray(this[0], v(e))
			}
			return v.inArray(e.jquery ? e[0] : e, this)
		},
		add: function(e, t) {
			var n = typeof e === "string" ? v(e, t) : v.makeArray(e && e.nodeType ? [e] : e),
				i = v.merge(this.get(), n);
			return this.pushStack(ce(n[0]) || ce(i[0]) ? i : v.unique(i))
		},
		addBack: function(e) {
			return this.add(e == null ? this.prevObject : this.prevObject.filter(e))
		}
	});
	v.fn.andSelf = v.fn.addBack;

	function ce(e) {
		return !e || !e.parentNode || e.parentNode.nodeType === 11
	}

	function ue(e, t) {
		do {
			e = e[t]
		} while (e && e.nodeType !== 1);
		return e
	}
	v.each({
		parent: function(e) {
			var t = e.parentNode;
			return t && t.nodeType !== 11 ? t : null
		},
		parents: function(e) {
			return v.dir(e, "parentNode")
		},
		parentsUntil: function(e, t, n) {
			return v.dir(e, "parentNode", n)
		},
		next: function(e) {
			return ue(e, "nextSibling")
		},
		prev: function(e) {
			return ue(e, "previousSibling")
		},
		nextAll: function(e) {
			return v.dir(e, "nextSibling")
		},
		prevAll: function(e) {
			return v.dir(e, "previousSibling")
		},
		nextUntil: function(e, t, n) {
			return v.dir(e, "nextSibling", n)
		},
		prevUntil: function(e, t, n) {
			return v.dir(e, "previousSibling", n)
		},
		siblings: function(e) {
			return v.sibling((e.parentNode || {}).firstChild, e)
		},
		children: function(e) {
			return v.sibling(e.firstChild)
		},
		contents: function(e) {
			return v.nodeName(e, "iframe") ? e.contentDocument || e.contentWindow.document : v.merge([], e.childNodes)
		}
	}, function(e, t) {
		v.fn[e] = function(n, i) {
			var r = v.map(this, t, n);
			if (!re.test(e)) {
				i = n
			}
			if (i && typeof i === "string") {
				r = v.filter(i, r)
			}
			r = this.length > 1 && !le[e] ? v.unique(r) : r;
			if (this.length > 1 && se.test(e)) {
				r = r.reverse()
			}
			return this.pushStack(r, e, d.call(arguments).join(","))
		}
	});
	v.extend({
		filter: function(e, t, n) {
			if (n) {
				e = ":not(" + e + ")"
			}
			return t.length === 1 ? v.find.matchesSelector(t[0], e) ? [t[0]] : [] : v.find.matches(e, t)
		},
		dir: function(e, t, n) {
			var r = [],
				s = e[t];
			while (s && s.nodeType !== 9 && (n === i || s.nodeType !== 1 || !v(s).is(n))) {
				if (s.nodeType === 1) {
					r.push(s)
				}
				s = s[t]
			}
			return r
		},
		sibling: function(e, t) {
			var n = [];
			for (; e; e = e.nextSibling) {
				if (e.nodeType === 1 && e !== t) {
					n.push(e)
				}
			}
			return n
		}
	});

	function fe(e, t, n) {
		t = t || 0;
		if (v.isFunction(t)) {
			return v.grep(e, function(e, i) {
				var r = !!t.call(e, i, e);
				return r === n
			})
		} else if (t.nodeType) {
			return v.grep(e, function(e, i) {
				return e === t === n
			})
		} else if (typeof t === "string") {
			var i = v.grep(e, function(e) {
				return e.nodeType === 1
			});
			if (ae.test(t)) {
				return v.filter(t, i, !n)
			} else {
				t = v.filter(t, i)
			}
		}
		return v.grep(e, function(e, i) {
			return v.inArray(e, t) >= 0 === n
		})
	}

	function de(e) {
		var t = pe.split("|"),
			n = e.createDocumentFragment();
		if (n.createElement) {
			while (t.length) {
				n.createElement(t.pop())
			}
		}
		return n
	}
	var pe = "abbr|article|aside|audio|bdi|canvas|data|datalist|details|figcaption|figure|footer|" + "header|hgroup|mark|meter|nav|output|progress|section|summary|time|video",
		he = / jQuery\d+="(?:null|\d+)"/g,
		me = /^\s+/,
		ge = /<(?!area|br|col|embed|hr|img|input|link|meta|param)(([\w:]+)[^>]*)\/>/gi,
		ve = /<([\w:]+)/,
		ye = /<tbody/i,
		be = /<|&#?\w+;/,
		we = /<(?:script|style|link)/i,
		xe = /<(?:script|object|embed|option|style)/i,
		Te = new RegExp("<(?:" + pe + ")[\\s/>]", "i"),
		ke = /^(?:checkbox|radio)$/,
		Ce = /checked\s*(?:[^=]|=\s*.checked.)/i,
		je = /\/(java|ecma)script/i,
		Ee = /^\s*<!(?:\[CDATA\[|\-\-)|[\]\-]{2}>\s*$/g,
		Se = {
			option: [1, "<select multiple='multiple'>", "</select>"],
			legend: [1, "<fieldset>", "</fieldset>"],
			thead: [1, "<table>", "</table>"],
			tr: [2, "<table><tbody>", "</tbody></table>"],
			td: [3, "<table><tbody><tr>", "</tr></tbody></table>"],
			col: [2, "<table><tbody></tbody><colgroup>", "</colgroup></table>"],
			area: [1, "<map>", "</map>"],
			_default: [0, "", ""]
		},
		Ne = de(a),
		Le = Ne.appendChild(a.createElement("div"));
	Se.optgroup = Se.option;
	Se.tbody = Se.tfoot = Se.colgroup = Se.caption = Se.thead;
	Se.th = Se.td;
	if (!v.support.htmlSerialize) {
		Se._default = [1, "X<div>", "</div>"]
	}
	v.fn.extend({
		text: function(e) {
			return v.access(this, function(e) {
				return e === i ? v.text(this) : this.empty().append((this[0] && this[0].ownerDocument || a).createTextNode(e))
			}, null, e, arguments.length)
		},
		wrapAll: function(e) {
			if (v.isFunction(e)) {
				return this.each(function(t) {
					v(this).wrapAll(e.call(this, t))
				})
			}
			if (this[0]) {
				var t = v(e, this[0].ownerDocument).eq(0).clone(true);
				if (this[0].parentNode) {
					t.insertBefore(this[0])
				}
				t.map(function() {
					var e = this;
					while (e.firstChild && e.firstChild.nodeType === 1) {
						e = e.firstChild
					}
					return e
				}).append(this)
			}
			return this
		},
		wrapInner: function(e) {
			if (v.isFunction(e)) {
				return this.each(function(t) {
					v(this).wrapInner(e.call(this, t))
				})
			}
			return this.each(function() {
				var t = v(this),
					n = t.contents();
				if (n.length) {
					n.wrapAll(e)
				} else {
					t.append(e)
				}
			})
		},
		wrap: function(e) {
			var t = v.isFunction(e);
			return this.each(function(n) {
				v(this).wrapAll(t ? e.call(this, n) : e)
			})
		},
		unwrap: function() {
			return this.parent().each(function() {
				if (!v.nodeName(this, "body")) {
					v(this).replaceWith(this.childNodes)
				}
			}).end()
		},
		append: function() {
			return this.domManip(arguments, true, function(e) {
				if (this.nodeType === 1 || this.nodeType === 11) {
					this.appendChild(e)
				}
			})
		},
		prepend: function() {
			return this.domManip(arguments, true, function(e) {
				if (this.nodeType === 1 || this.nodeType === 11) {
					this.insertBefore(e, this.firstChild)
				}
			})
		},
		before: function() {
			if (!ce(this[0])) {
				return this.domManip(arguments, false, function(e) {
					this.parentNode.insertBefore(e, this)
				})
			}
			if (arguments.length) {
				var e = v.clean(arguments);
				return this.pushStack(v.merge(e, this), "before", this.selector)
			}
		},
		after: function() {
			if (!ce(this[0])) {
				return this.domManip(arguments, false, function(e) {
					this.parentNode.insertBefore(e, this.nextSibling)
				})
			}
			if (arguments.length) {
				var e = v.clean(arguments);
				return this.pushStack(v.merge(this, e), "after", this.selector)
			}
		},
		remove: function(e, t) {
			var n, i = 0;
			for (;
				(n = this[i]) != null; i++) {
				if (!e || v.filter(e, [n]).length) {
					if (!t && n.nodeType === 1) {
						v.cleanData(n.getElementsByTagName("*"));
						v.cleanData([n])
					}
					if (n.parentNode) {
						n.parentNode.removeChild(n)
					}
				}
			}
			return this
		},
		empty: function() {
			var e, t = 0;
			for (;
				(e = this[t]) != null; t++) {
				if (e.nodeType === 1) {
					v.cleanData(e.getElementsByTagName("*"))
				}
				while (e.firstChild) {
					e.removeChild(e.firstChild)
				}
			}
			return this
		},
		clone: function(e, t) {
			e = e == null ? false : e;
			t = t == null ? e : t;
			return this.map(function() {
				return v.clone(this, e, t)
			})
		},
		html: function(e) {
			return v.access(this, function(e) {
				var t = this[0] || {},
					n = 0,
					r = this.length;
				if (e === i) {
					return t.nodeType === 1 ? t.innerHTML.replace(he, "") : i
				}
				if (typeof e === "string" && !we.test(e) && (v.support.htmlSerialize || !Te.test(e)) && (v.support.leadingWhitespace || !me.test(e)) && !Se[(ve.exec(e) || ["", ""])[1].toLowerCase()]) {
					e = e.replace(ge, "<$1></$2>");
					try {
						for (; n < r; n++) {
							t = this[n] || {};
							if (t.nodeType === 1) {
								v.cleanData(t.getElementsByTagName("*"));
								t.innerHTML = e
							}
						}
						t = 0
					} catch (s) {}
				}
				if (t) {
					this.empty().append(e)
				}
			}, null, e, arguments.length)
		},
		replaceWith: function(e) {
			if (!ce(this[0])) {
				if (v.isFunction(e)) {
					return this.each(function(t) {
						var n = v(this),
							i = n.html();
						n.replaceWith(e.call(this, t, i))
					})
				}
				if (typeof e !== "string") {
					e = v(e).detach()
				}
				return this.each(function() {
					var t = this.nextSibling,
						n = this.parentNode;
					v(this).remove();
					if (t) {
						v(t).before(e)
					} else {
						v(n).append(e)
					}
				})
			}
			return this.length ? this.pushStack(v(v.isFunction(e) ? e() : e), "replaceWith", e) : this
		},
		detach: function(e) {
			return this.remove(e, true)
		},
		domManip: function(e, t, n) {
			e = [].concat.apply([], e);
			var r, s, a, o, l = 0,
				c = e[0],
				u = [],
				f = this.length;
			if (!v.support.checkClone && f > 1 && typeof c === "string" && Ce.test(c)) {
				return this.each(function() {
					v(this).domManip(e, t, n)
				})
			}
			if (v.isFunction(c)) {
				return this.each(function(r) {
					var s = v(this);
					e[0] = c.call(this, r, t ? s.html() : i);
					s.domManip(e, t, n)
				})
			}
			if (this[0]) {
				r = v.buildFragment(e, this, u);
				a = r.fragment;
				s = a.firstChild;
				if (a.childNodes.length === 1) {
					a = s
				}
				if (s) {
					t = t && v.nodeName(s, "tr");
					for (o = r.cacheable || f - 1; l < f; l++) {
						n.call(t && v.nodeName(this[l], "table") ? Ie(this[l], "tbody") : this[l], l === o ? a : v.clone(a, true, true))
					}
				}
				a = s = null;
				if (u.length) {
					v.each(u, function(e, t) {
						if (t.src) {
							if (v.ajax) {
								v.ajax({
									url: t.src,
									type: "GET",
									dataType: "script",
									async: false,
									global: false,
									"throws": true
								})
							} else {
								v.error("no ajax")
							}
						} else {
							v.globalEval((t.text || t.textContent || t.innerHTML || "").replace(Ee, ""))
						} if (t.parentNode) {
							t.parentNode.removeChild(t)
						}
					})
				}
			}
			return this
		},
		outerHTML: function() {
			var e = this.first();
			var t = e.wrap("<div>").parent().html();
			e.unwrap();
			return t
		},
		setCaretTo: function(e, t) {
			if (t === i) {
				t = e
			}
			var n = this.get(0);
			if (n.createTextRange) {
				var r = n.createTextRange();
				r.collapse(true);
				r.moveStart("character", e);
				r.moveEnd("character", t - e);
				r.select()
			} else {
				n.focus();
				n.setSelectionRange(e, t)
			}
			return this
		}
	});

	function Ie(e, t) {
		return e.getElementsByTagName(t)[0] || e.appendChild(e.ownerDocument.createElement(t))
	}

	function Me(e, t) {
		if (t.nodeType !== 1 || !v.hasData(e)) {
			return
		}
		var n, i, r, s = v._data(e),
			a = v._data(t, s),
			o = s.events;
		if (o) {
			delete a.handle;
			a.events = {};
			for (n in o) {
				for (i = 0, r = o[n].length; i < r; i++) {
					v.event.add(t, n, o[n][i])
				}
			}
		}
		if (a.data) {
			a.data = v.extend({}, a.data)
		}
	}

	function Fe(e, t) {
		var n;
		if (t.nodeType !== 1) {
			return
		}
		if (t.clearAttributes) {
			t.clearAttributes()
		}
		if (t.mergeAttributes) {
			t.mergeAttributes(e)
		}
		n = t.nodeName.toLowerCase();
		if (n === "object") {
			if (t.parentNode) {
				t.outerHTML = e.outerHTML
			}
			if (v.support.html5Clone && (e.innerHTML && !v.trim(t.innerHTML))) {
				t.innerHTML = e.innerHTML
			}
		} else if (n === "input" && ke.test(e.type)) {
			t.defaultChecked = t.checked = e.checked;
			if (t.value !== e.value) {
				t.value = e.value
			}
		} else if (n === "option") {
			t.selected = e.defaultSelected
		} else if (n === "input" || n === "textarea") {
			t.defaultValue = e.defaultValue
		} else if (n === "script" && t.text !== e.text) {
			t.text = e.text
		}
		t.removeAttribute(v.expando)
	}
	v.buildFragment = function(e, t, n) {
		var r, s, o, l = e[0];
		t = t || a;
		t = !t.nodeType && t[0] || t;
		t = t.ownerDocument || t;
		if (e.length === 1 && typeof l === "string" && l.length < 512 && t === a && l.charAt(0) === "<" && !xe.test(l) && (v.support.checkClone || !Ce.test(l)) && (v.support.html5Clone || !Te.test(l))) {
			s = true;
			r = v.fragments[l];
			o = r !== i
		}
		if (!r) {
			r = t.createDocumentFragment();
			v.clean(e, t, r, n);
			if (s) {
				v.fragments[l] = o && r
			}
		}
		return {
			fragment: r,
			cacheable: s
		}
	};
	v.fragments = {};
	v.each({
		appendTo: "append",
		prependTo: "prepend",
		insertBefore: "before",
		insertAfter: "after",
		replaceAll: "replaceWith"
	}, function(e, t) {
		v.fn[e] = function(n) {
			var i, r = 0,
				s = [],
				a = v(n),
				o = a.length,
				l = this.length === 1 && this[0].parentNode;
			if ((l == null || l && l.nodeType === 11 && l.childNodes.length === 1) && o === 1) {
				a[t](this[0]);
				return this
			} else {
				for (; r < o; r++) {
					i = (r > 0 ? this.clone(true) : this).get();
					v(a[r])[t](i);
					s = s.concat(i)
				}
				return this.pushStack(s, e, a.selector)
			}
		}
	});

	function _e(e) {
		if (typeof e.getElementsByTagName !== "undefined") {
			return e.getElementsByTagName("*")
		} else if (typeof e.querySelectorAll !== "undefined") {
			return e.querySelectorAll("*")
		} else {
			return []
		}
	}

	function Ae(e) {
		if (ke.test(e.type)) {
			e.defaultChecked = e.checked
		}
	}
	v.extend({
		clone: function(e, t, n) {
			var i, r, s, a;
			if (v.support.html5Clone || v.isXMLDoc(e) || !Te.test("<" + e.nodeName + ">")) {
				a = e.cloneNode(true)
			} else {
				Le.innerHTML = e.outerHTML;
				Le.removeChild(a = Le.firstChild)
			} if ((!v.support.noCloneEvent || !v.support.noCloneChecked) && (e.nodeType === 1 || e.nodeType === 11) && !v.isXMLDoc(e)) {
				Fe(e, a);
				i = _e(e);
				r = _e(a);
				for (s = 0; i[s]; ++s) {
					if (r[s]) {
						Fe(i[s], r[s])
					}
				}
			}
			if (t) {
				Me(e, a);
				if (n) {
					i = _e(e);
					r = _e(a);
					for (s = 0; i[s]; ++s) {
						Me(i[s], r[s])
					}
				}
			}
			i = r = null;
			return a
		},
		clean: function(e, t, n, i) {
			var r, s, o, l, c, u, f, d, p, h, m, g, y = t === a && Ne,
				b = [];
			if (!t || typeof t.createDocumentFragment === "undefined") {
				t = a
			}
			for (r = 0;
				(o = e[r]) != null; r++) {
				if (typeof o === "number") {
					o += ""
				}
				if (!o) {
					continue
				}
				if (typeof o === "string") {
					if (!be.test(o)) {
						o = t.createTextNode(o)
					} else {
						y = y || de(t);
						f = t.createElement("div");
						y.appendChild(f);
						o = o.replace(ge, "<$1></$2>");
						l = (ve.exec(o) || ["", ""])[1].toLowerCase();
						c = Se[l] || Se._default;
						u = c[0];
						f.innerHTML = c[1] + o + c[2];
						while (u--) {
							f = f.lastChild
						}
						if (!v.support.tbody) {
							d = ye.test(o);
							p = l === "table" && !d ? f.firstChild && f.firstChild.childNodes : c[1] === "<table>" && !d ? f.childNodes : [];
							for (s = p.length - 1; s >= 0; --s) {
								if (v.nodeName(p[s], "tbody") && !p[s].childNodes.length) {
									p[s].parentNode.removeChild(p[s])
								}
							}
						}
						if (!v.support.leadingWhitespace && me.test(o)) {
							f.insertBefore(t.createTextNode(me.exec(o)[0]), f.firstChild)
						}
						o = f.childNodes;
						f.parentNode.removeChild(f)
					}
				}
				if (o.nodeType) {
					b.push(o)
				} else {
					v.merge(b, o)
				}
			}
			if (f) {
				o = f = y = null
			}
			if (!v.support.appendChecked) {
				for (r = 0;
					(o = b[r]) != null; r++) {
					if (v.nodeName(o, "input")) {
						Ae(o)
					} else if (typeof o.getElementsByTagName !== "undefined") {
						v.grep(o.getElementsByTagName("input"), Ae)
					}
				}
			}
			if (n) {
				m = function(e) {
					if (!e.type || je.test(e.type)) {
						return i ? i.push(e.parentNode ? e.parentNode.removeChild(e) : e) : n.appendChild(e)
					}
				};
				for (r = 0;
					(o = b[r]) != null; r++) {
					if (!(v.nodeName(o, "script") && m(o))) {
						n.appendChild(o);
						if (typeof o.getElementsByTagName !== "undefined") {
							g = v.grep(v.merge([], o.getElementsByTagName("script")), m);
							b.splice.apply(b, [r + 1, 0].concat(g));
							r += g.length
						}
					}
				}
			}
			return b
		},
		cleanData: function(e, t) {
			var n, i, r, s, a = 0,
				o = v.expando,
				l = v.cache,
				c = v.support.deleteExpando,
				u = v.event.special;
			for (;
				(r = e[a]) != null; a++) {
				if (t || v.acceptData(r)) {
					i = r[o];
					n = i && l[i];
					if (n) {
						if (n.events) {
							for (s in n.events) {
								if (u[s]) {
									v.event.remove(r, s)
								} else {
									v.removeEvent(r, s, n.handle)
								}
							}
						}
						if (l[i]) {
							delete l[i];
							if (c) {
								delete r[o]
							} else if (r.removeAttribute) {
								r.removeAttribute(o)
							} else {
								r[o] = null
							}
							v.deletedIds.push(i)
						}
					}
				}
			}
		}
	});
	(function() {
		var e, t;
		v.uaMatch = function(e) {
			e = e.toLowerCase();
			var t = /(chrome)[ \/]([\w.]+)/.exec(e) || /(webkit)[ \/]([\w.]+)/.exec(e) || /(opera)(?:.*version|)[ \/]([\w.]+)/.exec(e) || /(msie) ([\w.]+)/.exec(e) || e.indexOf("compatible") < 0 && /(mozilla)(?:.*? rv:([\w.]+)|)/.exec(e) || [];
			return {
				browser: t[1] || "",
				version: t[2] || "0"
			}
		};
		e = v.uaMatch(l.userAgent);
		t = {};
		if (e.browser) {
			t[e.browser] = true;
			t.version = e.version
		}
		if (t.chrome) {
			t.webkit = true
		} else if (t.webkit) {
			t.safari = true
		}
		v.browser = t;
		v.sub = function() {
			function e(t, n) {
				return new e.fn.init(t, n)
			}
			v.extend(true, e, this);
			e.superclass = this;
			e.fn = e.prototype = this();
			e.fn.constructor = e;
			e.sub = this.sub;
			e.fn.init = function n(i, r) {
				if (r && r instanceof v && !(r instanceof e)) {
					r = e(r)
				}
				return v.fn.init.call(this, i, r, t)
			};
			e.fn.init.prototype = e.fn;
			var t = e(a);
			return e
		}
	})();
	var $e, Be, De, Re = /alpha\([^)]*\)/i,
		He = /opacity=([^)]*)/,
		Pe = /^(top|right|bottom|left)$/,
		Oe = /^(none|table(?!-c[ea]).+)/,
		We = /^margin/,
		qe = new RegExp("^(" + y + ")(.*)$", "i"),
		Ue = new RegExp("^(" + y + ")(?!px)[a-z%]+$", "i"),
		ze = new RegExp("^([-+])=(" + y + ")", "i"),
		Ve = {
			BODY: "block"
		},
		Ge = {
			position: "absolute",
			visibility: "hidden",
			display: "block"
		},
		Xe = {
			letterSpacing: 0,
			fontWeight: 400
		},
		Ye = ["Top", "Right", "Bottom", "Left"],
		Qe = ["Webkit", "O", "Moz", "ms"],
		Ke = v.fn.toggle;

	function Je(e, t) {
		if (t in e) {
			return t
		}
		var n = t.charAt(0).toUpperCase() + t.slice(1),
			i = t,
			r = Qe.length;
		while (r--) {
			t = Qe[r] + n;
			if (t in e) {
				return t
			}
		}
		return i
	}

	function Ze(e, t) {
		e = t || e;
		return v.css(e, "display") === "none" || !v.contains(e.ownerDocument, e)
	}

	function et(e, t) {
		var n, i, r = [],
			s = 0,
			a = e.length;
		for (; s < a; s++) {
			n = e[s];
			if (!n.style) {
				continue
			}
			r[s] = v._data(n, "olddisplay");
			if (t) {
				if (!r[s] && n.style.display === "none") {
					n.style.display = ""
				}
				if (n.style.display === "" && Ze(n)) {
					r[s] = v._data(n, "olddisplay", rt(n.nodeName))
				}
			} else {
				i = $e(n, "display");
				if (!r[s] && i !== "none") {
					v._data(n, "olddisplay", i)
				}
			}
		}
		for (s = 0; s < a; s++) {
			n = e[s];
			if (!n.style) {
				continue
			}
			if (!t || n.style.display === "none" || n.style.display === "") {
				n.style.display = t ? r[s] || "" : "none"
			}
		}
		return e
	}
	v.fn.extend({
		css: function(e, t) {
			return v.access(this, function(e, t, n) {
				return n !== i ? v.style(e, t, n) : v.css(e, t)
			}, e, t, arguments.length > 1)
		},
		show: function() {
			return et(this, true)
		},
		hide: function() {
			return et(this)
		},
		toggle: function(e, t) {
			var n = typeof e === "boolean";
			if (v.isFunction(e) && v.isFunction(t)) {
				return Ke.apply(this, arguments)
			}
			return this.each(function() {
				if (n ? e : Ze(this)) {
					v(this).show()
				} else {
					v(this).hide()
				}
			})
		}
	});
	v.extend({
		cssHooks: {
			opacity: {
				get: function(e, t) {
					if (t) {
						var n = $e(e, "opacity");
						return n === "" ? "1" : n
					}
				}
			}
		},
		cssNumber: {
			fillOpacity: true,
			fontWeight: true,
			lineHeight: true,
			opacity: true,
			orphans: true,
			widows: true,
			zIndex: true,
			zoom: true
		},
		cssProps: {
			"float": v.support.cssFloat ? "cssFloat" : "styleFloat"
		},
		style: function(e, t, n, r) {
			if (!e || e.nodeType === 3 || e.nodeType === 8 || !e.style) {
				return
			}
			var s, a, o, l = v.camelCase(t),
				c = e.style;
			t = v.cssProps[l] || (v.cssProps[l] = Je(c, l));
			o = v.cssHooks[t] || v.cssHooks[l];
			if (n !== i) {
				a = typeof n;
				if (a === "string" && (s = ze.exec(n))) {
					n = (s[1] + 1) * s[2] + parseFloat(v.css(e, t));
					a = "number"
				}
				if (n == null || a === "number" && isNaN(n)) {
					return
				}
				if (a === "number" && !v.cssNumber[l]) {
					n += "px"
				}
				if (!o || !("set" in o) || (n = o.set(e, n, r)) !== i) {
					try {
						c[t] = n
					} catch (u) {}
				}
			} else {
				if (o && "get" in o && (s = o.get(e, false, r)) !== i) {
					return s
				}
				return c[t]
			}
		},
		css: function(e, t, n, r) {
			var s, a, o, l = v.camelCase(t);
			t = v.cssProps[l] || (v.cssProps[l] = Je(e.style, l));
			o = v.cssHooks[t] || v.cssHooks[l];
			if (o && "get" in o) {
				s = o.get(e, true, r)
			}
			if (s === i) {
				s = $e(e, t)
			}
			if (s === "normal" && t in Xe) {
				s = Xe[t]
			}
			if (n || r !== i) {
				a = parseFloat(s);
				return n || v.isNumeric(a) ? a || 0 : s
			}
			return s
		},
		swap: function(e, t, n) {
			var i, r, s = {};
			for (r in t) {
				s[r] = e.style[r];
				e.style[r] = t[r]
			}
			i = n.call(e);
			for (r in t) {
				e.style[r] = s[r]
			}
			return i
		}
	});
	if (window.getComputedStyle) {
		$e = function(e, t) {
			var n, i, r, s, a = window.getComputedStyle(e, null),
				o = e.style;
			if (a) {
				n = a.getPropertyValue(t) || a[t];
				if (n === "" && !v.contains(e.ownerDocument, e)) {
					n = v.style(e, t)
				}
				if (Ue.test(n) && We.test(t)) {
					i = o.width;
					r = o.minWidth;
					s = o.maxWidth;
					o.minWidth = o.maxWidth = o.width = n;
					n = a.width;
					o.width = i;
					o.minWidth = r;
					o.maxWidth = s
				}
			}
			return n
		}
	} else if (a.documentElement.currentStyle) {
		$e = function(e, t) {
			var n, i, r = e.currentStyle && e.currentStyle[t],
				s = e.style;
			if (r == null && s && s[t]) {
				r = s[t]
			}
			if (Ue.test(r) && !Pe.test(t)) {
				n = s.left;
				i = e.runtimeStyle && e.runtimeStyle.left;
				if (i) {
					e.runtimeStyle.left = e.currentStyle.left
				}
				s.left = t === "fontSize" ? "1em" : r;
				r = s.pixelLeft + "px";
				s.left = n;
				if (i) {
					e.runtimeStyle.left = i
				}
			}
			return r === "" ? "auto" : r
		}
	}

	function tt(e, t, n) {
		var i = qe.exec(t);
		return i ? Math.max(0, i[1] - (n || 0)) + (i[2] || "px") : t
	}

	function nt(e, t, n, i) {
		var r = n === (i ? "border" : "content") ? 4 : t === "width" ? 1 : 0,
			s = 0;
		for (; r < 4; r += 2) {
			if (n === "margin") {
				s += v.css(e, n + Ye[r], true)
			}
			if (i) {
				if (n === "content") {
					s -= parseFloat($e(e, "padding" + Ye[r])) || 0
				}
				if (n !== "margin") {
					s -= parseFloat($e(e, "border" + Ye[r] + "Width")) || 0
				}
			} else {
				s += parseFloat($e(e, "padding" + Ye[r])) || 0;
				if (n !== "padding") {
					s += parseFloat($e(e, "border" + Ye[r] + "Width")) || 0
				}
			}
		}
		return s
	}

	function it(e, t, n) {
		var i = t === "width" ? e.offsetWidth : e.offsetHeight,
			r = true,
			s = v.support.boxSizing && v.css(e, "boxSizing") === "border-box";
		if (i <= 0 || i == null) {
			i = $e(e, t);
			if (i < 0 || i == null) {
				i = e.style[t]
			}
			if (Ue.test(i)) {
				return i
			}
			r = s && (v.support.boxSizingReliable || i === e.style[t]);
			i = parseFloat(i) || 0
		}
		return i + nt(e, t, n || (s ? "border" : "content"), r) + "px"
	}

	function rt(e) {
		if (Ve[e]) {
			return Ve[e]
		}
		var t = v("<" + e + ">").appendTo(a.body),
			n = t.css("display");
		t.remove();
		if (n === "none" || n === "") {
			Be = a.body.appendChild(Be || v.extend(a.createElement("iframe"), {
				frameBorder: 0,
				width: 0,
				height: 0
			}));
			if (!De || !Be.createElement) {
				De = (Be.contentWindow || Be.contentDocument).document;
				De.write("<!doctype html><html><body>");
				De.close()
			}
			t = De.body.appendChild(De.createElement(e));
			n = $e(t, "display");
			a.body.removeChild(Be)
		}
		Ve[e] = n;
		return n
	}
	v.each(["height", "width"], function(e, t) {
		v.cssHooks[t] = {
			get: function(e, n, i) {
				if (n) {
					if (e.offsetWidth === 0 && Oe.test($e(e, "display"))) {
						return v.swap(e, Ge, function() {
							return it(e, t, i)
						})
					} else {
						return it(e, t, i)
					}
				}
			},
			set: function(e, n, i) {
				return tt(e, n, i ? nt(e, t, i, v.support.boxSizing && v.css(e, "boxSizing") === "border-box") : 0)
			}
		}
	});
	if (!v.support.opacity) {
		v.cssHooks.opacity = {
			get: function(e, t) {
				return He.test((t && e.currentStyle ? e.currentStyle.filter : e.style.filter) || "") ? .01 * parseFloat(RegExp.$1) + "" : t ? "1" : ""
			},
			set: function(e, t) {
				var n = e.style,
					i = e.currentStyle,
					r = v.isNumeric(t) ? "alpha(opacity=" + t * 100 + ")" : "",
					s = i && i.filter || n.filter || "";
				n.zoom = 1;
				if (t >= 1 && v.trim(s.replace(Re, "")) === "" && n.removeAttribute) {
					n.removeAttribute("filter");
					if (i && !i.filter) {
						return
					}
				}
				n.filter = Re.test(s) ? s.replace(Re, r) : s + " " + r
			}
		}
	}
	v(function() {
		if (!v.support.reliableMarginRight) {
			v.cssHooks.marginRight = {
				get: function(e, t) {
					return v.swap(e, {
						display: "inline-block"
					}, function() {
						if (t) {
							return $e(e, "marginRight")
						}
					})
				}
			}
		}
		if (!v.support.pixelPosition && v.fn.position) {
			v.each(["top", "left"], function(e, t) {
				v.cssHooks[t] = {
					get: function(e, n) {
						if (n) {
							var i = $e(e, t);
							return Ue.test(i) ? v(e).position()[t] + "px" : i
						}
					}
				}
			})
		}
	});
	if (v.expr && v.expr.filters) {
		v.expr.filters.hidden = function(e) {
			return e.offsetWidth === 0 && e.offsetHeight === 0 || !v.support.reliableHiddenOffsets && (e.style && e.style.display || $e(e, "display")) === "none"
		};
		v.expr.filters.visible = function(e) {
			return !v.expr.filters.hidden(e)
		}
	}
	v.each({
		margin: "",
		padding: "",
		border: "Width"
	}, function(e, t) {
		v.cssHooks[e + t] = {
			expand: function(n) {
				var i, r = typeof n === "string" ? n.split(" ") : [n],
					s = {};
				for (i = 0; i < 4; i++) {
					s[e + Ye[i] + t] = r[i] || r[i - 2] || r[0]
				}
				return s
			}
		};
		if (!We.test(e)) {
			v.cssHooks[e + t].set = tt
		}
	});
	var st = /%20/g,
		at = /\[\]$/,
		ot = /\r?\n/g,
		lt = /^(?:color|date|datetime|datetime-local|email|hidden|month|number|password|range|search|tel|text|time|url|week)$/i,
		ct = /^(?:select|textarea)/i;
	v.fn.extend({
		serialize: function() {
			return v.param(this.serializeArray())
		},
		serializeArray: function() {
			return this.map(function() {
				return this.elements ? v.makeArray(this.elements) : this
			}).filter(function() {
				return this.name && !this.disabled && (this.checked || ct.test(this.nodeName) || lt.test(this.type))
			}).map(function(e, t) {
				var n = v(this).val();
				return n == null ? null : v.isArray(n) ? v.map(n, function(e, n) {
					return {
						name: t.name,
						value: e.replace(ot, "\r\n")
					}
				}) : {
					name: t.name,
					value: n.replace(ot, "\r\n")
				}
			}).get()
		}
	});
	v.param = function(e, t) {
		var n, r = [],
			s = function(e, t) {
				t = v.isFunction(t) ? t() : t == null ? "" : t;
				r[r.length] = encodeURIComponent(e) + "=" + encodeURIComponent(t)
			};
		if (t === i) {
			t = v.ajaxSettings && v.ajaxSettings.traditional
		}
		if (v.isArray(e) || e.jquery && !v.isPlainObject(e)) {
			v.each(e, function() {
				s(this.name, this.value)
			})
		} else {
			for (n in e) {
				ut(n, e[n], t, s)
			}
		}
		return r.join("&").replace(st, "+")
	};

	function ut(e, t, n, i) {
		var r;
		if (v.isArray(t)) {
			v.each(t, function(t, r) {
				if (n || at.test(e)) {
					i(e, r)
				} else {
					ut(e + "[" + (typeof r === "object" ? t : "") + "]", r, n, i)
				}
			})
		} else if (!n && v.type(t) === "object") {
			for (r in t) {
				ut(e + "[" + r + "]", t[r], n, i)
			}
		} else {
			i(e, t)
		}
	}
	var ft, dt, pt = /#.*$/,
		ht = /^(.*?):[ \t]*([^\r\n]*)\r?$/gm,
		mt = /^(?:about|app|app\-storage|.+\-extension|file|res|widget):$/,
		gt = /^(?:GET|HEAD)$/,
		vt = /^\/\//,
		yt = /\?/,
		bt = /<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi,
		wt = /([?&])_=[^&]*/,
		xt = /^([\w\+\.\-]+:)(?:\/\/([^\/?#:]*)(?::(\d+)|)|)/,
		Tt = v.fn.load,
		kt = {},
		Ct = {},
		jt = ["*/"] + ["*"];
	try {
		dt = o.href
	} catch (Et) {
		dt = a.createElement("a");
		dt.href = "";
		dt = dt.href
	}
	ft = xt.exec(dt.toLowerCase()) || [];

	function St(e) {
		return function(t, n) {
			if (typeof t !== "string") {
				n = t;
				t = "*"
			}
			var i, r, s, a = t.toLowerCase().split(w),
				o = 0,
				l = a.length;
			if (v.isFunction(n)) {
				for (; o < l; o++) {
					i = a[o];
					s = /^\+/.test(i);
					if (s) {
						i = i.substr(1) || "*"
					}
					r = e[i] = e[i] || [];
					r[s ? "unshift" : "push"](n)
				}
			}
		}
	}

	function Nt(e, t, n, r, s, a) {
		s = s || t.dataTypes[0];
		a = a || {};
		a[s] = true;
		var o, l = e[s],
			c = 0,
			u = l ? l.length : 0,
			f = e === kt;
		for (; c < u && (f || !o); c++) {
			o = l[c](t, n, r);
			if (typeof o === "string") {
				if (!f || a[o]) {
					o = i
				} else {
					t.dataTypes.unshift(o);
					o = Nt(e, t, n, r, o, a)
				}
			}
		}
		if ((f || !o) && !a["*"]) {
			o = Nt(e, t, n, r, "*", a)
		}
		return o
	}

	function Lt(e, t) {
		var n, r, s = v.ajaxSettings.flatOptions || {};
		for (n in t) {
			if (t[n] !== i) {
				(s[n] ? e : r || (r = {}))[n] = t[n]
			}
		}
		if (r) {
			v.extend(true, e, r)
		}
	}
	v.fn.load = function(e, t, n) {
		if (typeof e !== "string" && Tt) {
			return Tt.apply(this, arguments)
		}
		if (!this.length) {
			return this
		}
		var r, s, a, o = this,
			l = e.indexOf(" ");
		if (l >= 0) {
			r = e.slice(l, e.length);
			e = e.slice(0, l)
		}
		if (v.isFunction(t)) {
			n = t;
			t = i
		} else if (t && typeof t === "object") {
			s = "POST"
		}
		v.ajax({
			url: e,
			type: s,
			dataType: "html",
			data: t,
			complete: function(e, t) {
				if (n) {
					o.each(n, a || [e.responseText, t, e])
				}
			}
		}).done(function(e) {
			a = arguments;
			o.html(r ? v("<div>").append(e.replace(bt, "")).find(r) : e)
		});
		return this
	};
	v.each("ajaxStart ajaxStop ajaxComplete ajaxError ajaxSuccess ajaxSend".split(" "), function(e, t) {
		v.fn[t] = function(e) {
			return this.on(t, e)
		}
	});
	v.each(["get", "post"], function(e, t) {
		v[t] = function(e, n, r, s) {
			if (v.isFunction(n)) {
				s = s || r;
				r = n;
				n = i
			}
			return v.ajax({
				type: t,
				url: e,
				data: n,
				success: r,
				dataType: s
			})
		}
	});
	v.extend({
		getScript: function(e, t) {
			return v.get(e, i, t, "script")
		},
		getJSON: function(e, t, n) {
			return v.get(e, t, n, "json")
		},
		ajaxSetup: function(e, t) {
			if (t) {
				Lt(e, v.ajaxSettings)
			} else {
				t = e;
				e = v.ajaxSettings
			}
			Lt(e, t);
			return e
		},
		ajaxSettings: {
			url: dt,
			isLocal: mt.test(ft[1]),
			global: true,
			type: "GET",
			contentType: "application/x-www-form-urlencoded; charset=UTF-8",
			processData: true,
			async: true,
			accepts: {
				xml: "application/xml, text/xml",
				html: "text/html",
				text: "text/plain",
				json: "application/json, text/javascript",
				"*": jt
			},
			contents: {
				xml: /xml/,
				html: /html/,
				json: /json/
			},
			responseFields: {
				xml: "responseXML",
				text: "responseText"
			},
			converters: {
				"* text": window.String,
				"text html": true,
				"text json": v.parseJSON,
				"text xml": v.parseXML
			},
			flatOptions: {
				context: true,
				url: true
			}
		},
		ajaxPrefilter: St(kt),
		ajaxTransport: St(Ct),
		ajax: function(e, t) {
			if (typeof e === "object") {
				t = e;
				e = i
			}
			t = t || {};
			var n, r, s, a, o, l, c, u, f = v.ajaxSetup({}, t),
				d = f.context || f,
				p = d !== f && (d.nodeType || d instanceof v) ? v(d) : v.event,
				h = v.Deferred(),
				m = v.Callbacks("once memory"),
				g = f.statusCode || {},
				y = {},
				b = {},
				x = 0,
				T = "canceled",
				k = {
					readyState: 0,
					setRequestHeader: function(e, t) {
						if (!x) {
							var n = e.toLowerCase();
							e = b[n] = b[n] || e;
							y[e] = t
						}
						return this
					},
					getAllResponseHeaders: function() {
						return x === 2 ? r : null
					},
					getResponseHeader: function(e) {
						var t;
						if (x === 2) {
							if (!s) {
								s = {};
								while (t = ht.exec(r)) {
									s[t[1].toLowerCase()] = t[2]
								}
							}
							t = s[e.toLowerCase()]
						}
						return t === i ? null : t
					},
					overrideMimeType: function(e) {
						if (!x) {
							f.mimeType = e
						}
						return this
					},
					abort: function(e) {
						e = e || T;
						if (a) {
							a.abort(e)
						}
						C(0, e);
						return this
					}
				};

			function C(e, t, s, l) {
				var u, y, b, w, T, C = t;
				if (x === 2) {
					return
				}
				x = 2;
				if (o) {
					clearTimeout(o)
				}
				a = i;
				r = l || "";
				k.readyState = e > 0 ? 4 : 0;
				if (s) {
					w = It(f, k, s)
				}
				if (e >= 200 && e < 300 || e === 304) {
					if (f.ifModified) {
						T = k.getResponseHeader("Last-Modified");
						if (T) {
							v.lastModified[n] = T
						}
						T = k.getResponseHeader("Etag");
						if (T) {
							v.etag[n] = T
						}
					}
					if (e === 304) {
						C = "notmodified";
						u = true
					} else {
						u = Mt(f, w);
						C = u.state;
						y = u.data;
						b = u.error;
						u = !b
					}
				} else {
					b = C;
					if (!C || e) {
						C = "error";
						if (e < 0) {
							e = 0
						}
					}
				}
				k.status = e;
				k.statusText = (t || C) + "";
				if (u) {
					h.resolveWith(d, [y, C, k])
				} else {
					h.rejectWith(d, [k, C, b])
				}
				k.statusCode(g);
				g = i;
				if (c) {
					p.trigger("ajax" + (u ? "Success" : "Error"), [k, f, u ? y : b])
				}
				m.fireWith(d, [k, C]);
				if (c) {
					p.trigger("ajaxComplete", [k, f]);
					if (!--v.active) {
						v.event.trigger("ajaxStop")
					}
				}
			}
			h.promise(k);
			k.success = k.done;
			k.error = k.fail;
			k.complete = m.add;
			k.statusCode = function(e) {
				if (e) {
					var t;
					if (x < 2) {
						for (t in e) {
							g[t] = [g[t], e[t]]
						}
					} else {
						t = e[k.status];
						k.always(t)
					}
				}
				return this
			};
			f.url = ((e || f.url) + "").replace(pt, "").replace(vt, ft[1] + "//");
			f.dataTypes = v.trim(f.dataType || "*").toLowerCase().split(w);
			if (f.crossDomain == null) {
				l = xt.exec(f.url.toLowerCase());
				f.crossDomain = !!(l && (l[1] !== ft[1] || l[2] !== ft[2] || (l[3] || (l[1] === "http:" ? 80 : 443)) != (ft[3] || (ft[1] === "http:" ? 80 : 443))))
			}
			if (f.data && f.processData && typeof f.data !== "string") {
				f.data = v.param(f.data, f.traditional)
			}
			Nt(kt, f, t, k);
			if (x === 2) {
				return k
			}
			c = f.global;
			f.type = f.type.toUpperCase();
			f.hasContent = !gt.test(f.type);
			if (c && v.active++ === 0) {
				v.event.trigger("ajaxStart")
			}
			if (!f.hasContent) {
				if (f.data) {
					f.url += (yt.test(f.url) ? "&" : "?") + f.data;
					delete f.data
				}
				n = f.url;
				if (f.cache === false) {
					var j = v.now(),
						E = f.url.replace(wt, "$1_=" + j);
					f.url = E + (E === f.url ? (yt.test(f.url) ? "&" : "?") + "_=" + j : "")
				}
			}
			if (f.data && f.hasContent && f.contentType !== false || t.contentType) {
				k.setRequestHeader("Content-Type", f.contentType)
			}
			if (f.ifModified) {
				n = n || f.url;
				if (v.lastModified[n]) {
					k.setRequestHeader("If-Modified-Since", v.lastModified[n])
				}
				if (v.etag[n]) {
					k.setRequestHeader("If-None-Match", v.etag[n])
				}
			}
			k.setRequestHeader("Accept", f.dataTypes[0] && f.accepts[f.dataTypes[0]] ? f.accepts[f.dataTypes[0]] + (f.dataTypes[0] !== "*" ? ", " + jt + "; q=0.01" : "") : f.accepts["*"]);
			for (u in f.headers) {
				k.setRequestHeader(u, f.headers[u])
			}
			if (f.beforeSend && (f.beforeSend.call(d, k, f) === false || x === 2)) {
				return k.abort()
			}
			T = "abort";
			for (u in {
				success: 1,
				error: 1,
				complete: 1
			}) {
				k[u](f[u])
			}
			a = Nt(Ct, f, t, k);
			if (!a) {
				C(-1, "No Transport")
			} else {
				k.readyState = 1;
				if (c) {
					p.trigger("ajaxSend", [k, f])
				}
				if (f.async && f.timeout > 0) {
					o = setTimeout(function() {
						k.abort("timeout")
					}, f.timeout)
				}
				try {
					x = 1;
					a.send(y, C)
				} catch (S) {
					if (x < 2) {
						C(-1, S)
					} else {
						throw S
					}
				}
			}
			return k
		},
		active: 0,
		lastModified: {},
		etag: {}
	});

	function It(e, t, n) {
		var r, s, a, o, l = e.contents,
			c = e.dataTypes,
			u = e.responseFields;
		for (s in u) {
			if (s in n) {
				t[u[s]] = n[s]
			}
		}
		while (c[0] === "*") {
			c.shift();
			if (r === i) {
				r = e.mimeType || t.getResponseHeader("content-type")
			}
		}
		if (r) {
			for (s in l) {
				if (l[s] && l[s].test(r)) {
					c.unshift(s);
					break
				}
			}
		}
		if (c[0] in n) {
			a = c[0]
		} else {
			for (s in n) {
				if (!c[0] || e.converters[s + " " + c[0]]) {
					a = s;
					break
				}
				if (!o) {
					o = s
				}
			}
			a = a || o
		} if (a) {
			if (a !== c[0]) {
				c.unshift(a)
			}
			return n[a]
		}
	}

	function Mt(e, t) {
		var n, i, r, s, a = e.dataTypes.slice(),
			o = a[0],
			l = {},
			c = 0;
		if (e.dataFilter) {
			t = e.dataFilter(t, e.dataType)
		}
		if (a[1]) {
			for (n in e.converters) {
				l[n.toLowerCase()] = e.converters[n]
			}
		}
		for (; r = a[++c];) {
			if (r !== "*") {
				if (o !== "*" && o !== r) {
					n = l[o + " " + r] || l["* " + r];
					if (!n) {
						for (i in l) {
							s = i.split(" ");
							if (s[1] === r) {
								n = l[o + " " + s[0]] || l["* " + s[0]];
								if (n) {
									if (n === true) {
										n = l[i]
									} else if (l[i] !== true) {
										r = s[0];
										a.splice(c--, 0, r)
									}
									break
								}
							}
						}
					}
					if (n !== true) {
						if (n && e["throws"]) {
							t = n(t)
						} else {
							try {
								t = n(t)
							} catch (u) {
								return {
									state: "parsererror",
									error: n ? u : "No conversion from " + o + " to " + r
								}
							}
						}
					}
				}
				o = r
			}
		}
		return {
			state: "success",
			data: t
		}
	}
	var Ft = [],
		_t = /\?/,
		At = /(=)\?(?=&|$)|\?\?/,
		$t = v.now();
	v.ajaxSetup({
		jsonp: "callback",
		jsonpCallback: function() {
			var e = Ft.pop() || v.expando + "_" + $t++;
			this[e] = true;
			return e
		}
	});
	v.ajaxPrefilter("json jsonp", function(e, t, n) {
		var r, s, a, o = e.data,
			l = e.url,
			c = e.jsonp !== false,
			u = c && At.test(l),
			f = c && !u && typeof o === "string" && !(e.contentType || "").indexOf("application/x-www-form-urlencoded") && At.test(o);
		if (e.dataTypes[0] === "jsonp" || u || f) {
			r = e.jsonpCallback = v.isFunction(e.jsonpCallback) ? e.jsonpCallback() : e.jsonpCallback;
			s = window[r];
			if (u) {
				e.url = l.replace(At, "$1" + r)
			} else if (f) {
				e.data = o.replace(At, "$1" + r)
			} else if (c) {
				e.url += (_t.test(l) ? "&" : "?") + e.jsonp + "=" + r
			}
			e.converters["script json"] = function() {
				if (!a) {
					v.error(r + " was not called")
				}
				return a[0]
			};
			e.dataTypes[0] = "json";
			window[r] = function() {
				a = arguments
			};
			n.always(function() {
				window[r] = s;
				if (e[r]) {
					e.jsonpCallback = t.jsonpCallback;
					Ft.push(r)
				}
				if (a && v.isFunction(s)) {
					s(a[0])
				}
				a = s = i
			});
			return "script"
		}
	});
	v.ajaxSetup({
		accepts: {
			script: "text/javascript, application/javascript, application/ecmascript, application/x-ecmascript"
		},
		contents: {
			script: /javascript|ecmascript/
		},
		converters: {
			"text script": function(e) {
				v.globalEval(e);
				return e
			}
		}
	});
	v.ajaxPrefilter("script", function(e) {
		if (e.cache === i) {
			e.cache = false
		}
		if (e.crossDomain) {
			e.type = "GET";
			e.global = false
		}
	});
	v.ajaxTransport("script", function(e) {
		if (e.crossDomain) {
			var t, n = a.head || a.getElementsByTagName("head")[0] || a.documentElement;
			return {
				send: function(r, s) {
					t = a.createElement("script");
					t.async = "async";
					if (e.scriptCharset) {
						t.charset = e.scriptCharset
					}
					t.src = e.url;
					t.onload = t.onreadystatechange = function(e, r) {
						if (r || !t.readyState || /loaded|complete/.test(t.readyState)) {
							t.onload = t.onreadystatechange = null;
							if (n && t.parentNode) {
								n.removeChild(t)
							}
							t = i;
							if (!r) {
								s(200, "success")
							}
						}
					};
					n.insertBefore(t, n.firstChild)
				},
				abort: function() {
					if (t) {
						t.onload(0, 1)
					}
				}
			}
		}
	});
	var Bt, Dt = window.ActiveXObject ? function() {
			for (var e in Bt) {
				Bt[e](0, 1)
			}
		} : false,
		Rt = 0;

	function Ht() {
		try {
			return new window.XMLHttpRequest
		} catch (e) {}
	}

	function Pt() {
		try {
			return new window.ActiveXObject("Microsoft.XMLHTTP")
		} catch (e) {}
	}
	v.ajaxSettings.xhr = window.ActiveXObject ? function() {
		return !this.isLocal && Ht() || Pt()
	} : Ht;
	(function(e) {
		v.extend(v.support, {
			ajax: !!e,
			cors: !!e && "withCredentials" in e
		})
	})(v.ajaxSettings.xhr());
	if (v.support.ajax) {
		v.ajaxTransport(function(e) {
			if (!e.crossDomain || v.support.cors) {
				var t;
				return {
					send: function(n, r) {
						var s, a, o = e.xhr();
						if (e.username) {
							o.open(e.type, e.url, e.async, e.username, e.password)
						} else {
							o.open(e.type, e.url, e.async)
						} if (e.xhrFields) {
							for (a in e.xhrFields) {
								o[a] = e.xhrFields[a]
							}
						}
						if (e.mimeType && o.overrideMimeType) {
							o.overrideMimeType(e.mimeType)
						}
						if (!e.crossDomain && !n["X-Requested-With"]) {
							n["X-Requested-With"] = "XMLHttpRequest"
						}
						try {
							for (a in n) {
								o.setRequestHeader(a, n[a])
							}
						} catch (l) {}
						o.send(e.hasContent && e.data || null);
						t = function(n, a) {
							var l, c, u, f, d;
							try {
								if (t && (a || o.readyState === 4)) {
									t = i;
									if (s) {
										o.onreadystatechange = v.noop;
										if (Dt) {
											delete Bt[s]
										}
									}
									if (a) {
										if (o.readyState !== 4) {
											o.abort()
										}
									} else {
										l = o.status;
										u = o.getAllResponseHeaders();
										f = {};
										d = o.responseXML;
										if (d && d.documentElement) {
											f.xml = d
										}
										try {
											f.text = o.responseText
										} catch (p) {}
										try {
											c = o.statusText
										} catch (p) {
											c = ""
										}
										if (!l && e.isLocal && !e.crossDomain) {
											l = f.text ? 200 : 404
										} else if (l === 1223) {
											l = 204
										}
									}
								}
							} catch (h) {
								if (!a) {
									r(-1, h)
								}
							}
							if (f) {
								r(l, c, f, u)
							}
						};
						if (!e.async) {
							t()
						} else if (o.readyState === 4) {
							setTimeout(t, 0)
						} else {
							s = ++Rt;
							if (Dt) {
								if (!Bt) {
									Bt = {};
									v(window).unload(Dt)
								}
								Bt[s] = t
							}
							o.onreadystatechange = t
						}
					},
					abort: function() {
						if (t) {
							t(0, 1)
						}
					}
				}
			}
		})
	}
	var Ot, Wt, qt = /^(?:toggle|show|hide)$/,
		Ut = new RegExp("^(?:([-+])=|)(" + y + ")([a-z%]*)$", "i"),
		zt = /queueHooks$/,
		Vt = [Jt],
		Gt = {
			"*": [
				function(e, t) {
					var n, i, r = this.createTween(e, t),
						s = Ut.exec(t),
						a = r.cur(),
						o = +a || 0,
						l = 1,
						c = 20;
					if (s) {
						n = +s[2];
						i = s[3] || (v.cssNumber[e] ? "" : "px");
						if (i !== "px" && o) {
							o = v.css(r.elem, e, true) || n || 1;
							do {
								l = l || ".5";
								o = o / l;
								v.style(r.elem, e, o + i)
							} while (l !== (l = r.cur() / a) && l !== 1 && --c)
						}
						r.unit = i;
						r.start = o;
						r.end = s[1] ? o + (s[1] + 1) * n : n
					}
					return r
				}
			]
		};

	function Xt() {
		setTimeout(function() {
			Ot = i
		}, 0);
		return Ot = v.now()
	}

	function Yt(e, t) {
		v.each(t, function(t, n) {
			var i = (Gt[t] || []).concat(Gt["*"]),
				r = 0,
				s = i.length;
			for (; r < s; r++) {
				if (i[r].call(e, t, n)) {
					return
				}
			}
		})
	}

	function Qt(e, t, n) {
		var i, r = 0,
			s = 0,
			a = Vt.length,
			o = v.Deferred().always(function() {
				delete l.elem
			}),
			l = function() {
				var t = Ot || Xt(),
					n = Math.max(0, c.startTime + c.duration - t),
					i = n / c.duration || 0,
					r = 1 - i,
					s = 0,
					a = c.tweens.length;
				for (; s < a; s++) {
					c.tweens[s].run(r)
				}
				o.notifyWith(e, [c, r, n]);
				if (r < 1 && a) {
					return n
				} else {
					o.resolveWith(e, [c]);
					return false
				}
			},
			c = o.promise({
				elem: e,
				props: v.extend({}, t),
				opts: v.extend(true, {
					specialEasing: {}
				}, n),
				originalProperties: t,
				originalOptions: n,
				startTime: Ot || Xt(),
				duration: n.duration,
				tweens: [],
				createTween: function(t, n, i) {
					var r = v.Tween(e, c.opts, t, n, c.opts.specialEasing[t] || c.opts.easing);
					c.tweens.push(r);
					return r
				},
				stop: function(t) {
					var n = 0,
						i = t ? c.tweens.length : 0;
					for (; n < i; n++) {
						c.tweens[n].run(1)
					}
					if (t) {
						o.resolveWith(e, [c, t])
					} else {
						o.rejectWith(e, [c, t])
					}
					return this
				}
			}),
			u = c.props;
		Kt(u, c.opts.specialEasing);
		for (; r < a; r++) {
			i = Vt[r].call(c, e, u, c.opts);
			if (i) {
				return i
			}
		}
		Yt(c, u);
		if (v.isFunction(c.opts.start)) {
			c.opts.start.call(e, c)
		}
		v.fx.timer(v.extend(l, {
			anim: c,
			queue: c.opts.queue,
			elem: e
		}));
		return c.progress(c.opts.progress).done(c.opts.done, c.opts.complete).fail(c.opts.fail).always(c.opts.always)
	}

	function Kt(e, t) {
		var n, i, r, s, a;
		for (n in e) {
			i = v.camelCase(n);
			r = t[i];
			s = e[n];
			if (v.isArray(s)) {
				r = s[1];
				s = e[n] = s[0]
			}
			if (n !== i) {
				e[i] = s;
				delete e[n]
			}
			a = v.cssHooks[i];
			if (a && "expand" in a) {
				s = a.expand(s);
				delete e[i];
				for (n in s) {
					if (!(n in e)) {
						e[n] = s[n];
						t[n] = r
					}
				}
			} else {
				t[i] = r
			}
		}
	}
	v.Animation = v.extend(Qt, {
		tweener: function(e, t) {
			if (v.isFunction(e)) {
				t = e;
				e = ["*"]
			} else {
				e = e.split(" ")
			}
			var n, i = 0,
				r = e.length;
			for (; i < r; i++) {
				n = e[i];
				Gt[n] = Gt[n] || [];
				Gt[n].unshift(t)
			}
		},
		prefilter: function(e, t) {
			if (t) {
				Vt.unshift(e)
			} else {
				Vt.push(e)
			}
		}
	});

	function Jt(e, t, n) {
		var i, r, s, a, o, l, c, u, f, d = this,
			p = e.style,
			h = {},
			m = [],
			g = e.nodeType && Ze(e);
		if (!n.queue) {
			u = v._queueHooks(e, "fx");
			if (u.unqueued == null) {
				u.unqueued = 0;
				f = u.empty.fire;
				u.empty.fire = function() {
					if (!u.unqueued) {
						f()
					}
				}
			}
			u.unqueued++;
			d.always(function() {
				d.always(function() {
					u.unqueued--;
					if (!v.queue(e, "fx").length) {
						u.empty.fire()
					}
				})
			})
		}
		if (e.nodeType === 1 && ("height" in t || "width" in t)) {
			n.overflow = [p.overflow, p.overflowX, p.overflowY];
			if (v.css(e, "display") === "inline" && v.css(e, "float") === "none") {
				if (!v.support.inlineBlockNeedsLayout || rt(e.nodeName) === "inline") {
					p.display = "inline-block"
				} else {
					p.zoom = 1
				}
			}
		}
		if (n.overflow) {
			p.overflow = "hidden";
			if (!v.support.shrinkWrapBlocks) {
				d.done(function() {
					p.overflow = n.overflow[0];
					p.overflowX = n.overflow[1];
					p.overflowY = n.overflow[2]
				})
			}
		}
		for (i in t) {
			s = t[i];
			if (qt.exec(s)) {
				delete t[i];
				l = l || s === "toggle";
				if (s === (g ? "hide" : "show")) {
					continue
				}
				m.push(i)
			}
		}
		a = m.length;
		if (a) {
			o = v._data(e, "fxshow") || v._data(e, "fxshow", {});
			if ("hidden" in o) {
				g = o.hidden
			}
			if (l) {
				o.hidden = !g
			}
			if (g) {
				v(e).show()
			} else {
				d.done(function() {
					v(e).hide()
				})
			}
			d.done(function() {
				var t;
				v.removeData(e, "fxshow", true);
				for (t in h) {
					v.style(e, t, h[t])
				}
			});
			for (i = 0; i < a; i++) {
				r = m[i];
				c = d.createTween(r, g ? o[r] : 0);
				h[r] = o[r] || v.style(e, r);
				if (!(r in o)) {
					o[r] = c.start;
					if (g) {
						c.end = c.start;
						c.start = r === "width" || r === "height" ? 1 : 0
					}
				}
			}
		}
	}

	function Zt(e, t, n, i, r) {
		return new Zt.prototype.init(e, t, n, i, r)
	}
	v.Tween = Zt;
	Zt.prototype = {
		constructor: Zt,
		init: function(e, t, n, i, r, s) {
			this.elem = e;
			this.prop = n;
			this.easing = r || "swing";
			this.options = t;
			this.start = this.now = this.cur();
			this.end = i;
			this.unit = s || (v.cssNumber[n] ? "" : "px")
		},
		cur: function() {
			var e = Zt.propHooks[this.prop];
			return e && e.get ? e.get(this) : Zt.propHooks._default.get(this)
		},
		run: function(e) {
			var t, n = Zt.propHooks[this.prop];
			if (this.options.duration) {
				this.pos = t = v.easing[this.easing](e, this.options.duration * e, 0, 1, this.options.duration)
			} else {
				this.pos = t = e
			}
			this.now = (this.end - this.start) * t + this.start;
			if (this.options.step) {
				this.options.step.call(this.elem, this.now, this)
			}
			if (n && n.set) {
				n.set(this)
			} else {
				Zt.propHooks._default.set(this)
			}
			return this
		}
	};
	Zt.prototype.init.prototype = Zt.prototype;
	Zt.propHooks = {
		_default: {
			get: function(e) {
				var t;
				if (e.elem[e.prop] != null && (!e.elem.style || e.elem.style[e.prop] == null)) {
					return e.elem[e.prop]
				}
				t = v.css(e.elem, e.prop, false, "");
				return !t || t === "auto" ? 0 : t
			},
			set: function(e) {
				if (v.fx.step[e.prop]) {
					v.fx.step[e.prop](e)
				} else if (e.elem.style && (e.elem.style[v.cssProps[e.prop]] != null || v.cssHooks[e.prop])) {
					v.style(e.elem, e.prop, e.now + e.unit)
				} else {
					e.elem[e.prop] = e.now
				}
			}
		}
	};
	Zt.propHooks.scrollTop = Zt.propHooks.scrollLeft = {
		set: function(e) {
			if (e.elem.nodeType && e.elem.parentNode) {
				e.elem[e.prop] = e.now
			}
		}
	};
	v.each(["toggle", "show", "hide"], function(e, t) {
		var n = v.fn[t];
		v.fn[t] = function(i, r, s) {
			return i == null || typeof i === "boolean" || !e && v.isFunction(i) && v.isFunction(r) ? n.apply(this, arguments) : this.animate(en(t, true), i, r, s)
		}
	});
	v.fn.extend({
		fadeTo: function(e, t, n, i) {
			return this.filter(Ze).css("opacity", 0).show().end().animate({
				opacity: t
			}, e, n, i)
		},
		animate: function(e, t, n, i) {
			var r = v.isEmptyObject(e),
				s = v.speed(t, n, i),
				a = function() {
					var t = Qt(this, v.extend({}, e), s);
					if (r) {
						t.stop(true)
					}
				};
			return r || s.queue === false ? this.each(a) : this.queue(s.queue, a)
		},
		stop: function(e, t, n) {
			var r = function(e) {
				var t = e.stop;
				delete e.stop;
				t(n)
			};
			if (typeof e !== "string") {
				n = t;
				t = e;
				e = i
			}
			if (t && e !== false) {
				this.queue(e || "fx", [])
			}
			return this.each(function() {
				var t = true,
					i = e != null && e + "queueHooks",
					s = v.timers,
					a = v._data(this);
				if (i) {
					if (a[i] && a[i].stop) {
						r(a[i])
					}
				} else {
					for (i in a) {
						if (a[i] && a[i].stop && zt.test(i)) {
							r(a[i])
						}
					}
				}
				for (i = s.length; i--;) {
					if (s[i].elem === this && (e == null || s[i].queue === e)) {
						s[i].anim.stop(n);
						t = false;
						s.splice(i, 1)
					}
				}
				if (t || !n) {
					v.dequeue(this, e)
				}
			})
		}
	});

	function en(e, t) {
		var n, i = {
				height: e
			},
			r = 0;
		t = t ? 1 : 0;
		for (; r < 4; r += 2 - t) {
			n = Ye[r];
			i["margin" + n] = i["padding" + n] = e
		}
		if (t) {
			i.opacity = i.width = e
		}
		return i
	}
	v.each({
		slideDown: en("show"),
		slideUp: en("hide"),
		slideToggle: en("toggle"),
		fadeIn: {
			opacity: "show"
		},
		fadeOut: {
			opacity: "hide"
		},
		fadeToggle: {
			opacity: "toggle"
		}
	}, function(e, t) {
		v.fn[e] = function(e, n, i) {
			return this.animate(t, e, n, i)
		}
	});
	v.speed = function(e, t, n) {
		var i = e && typeof e === "object" ? v.extend({}, e) : {
			complete: n || !n && t || v.isFunction(e) && e,
			duration: e,
			easing: n && t || t && !v.isFunction(t) && t
		};
		i.duration = v.fx.off ? 0 : typeof i.duration === "number" ? i.duration : i.duration in v.fx.speeds ? v.fx.speeds[i.duration] : v.fx.speeds._default;
		if (i.queue == null || i.queue === true) {
			i.queue = "fx"
		}
		i.old = i.complete;
		i.complete = function() {
			if (v.isFunction(i.old)) {
				i.old.call(this)
			}
			if (i.queue) {
				v.dequeue(this, i.queue)
			}
		};
		return i
	};
	v.easing = {
		linear: function(e) {
			return e
		},
		swing: function(e) {
			return .5 - Math.cos(e * Math.PI) / 2
		}
	};
	v.timers = [];
	v.fx = Zt.prototype.init;
	v.fx.tick = function() {
		var e, t = v.timers,
			n = 0;
		Ot = v.now();
		for (; n < t.length; n++) {
			e = t[n];
			if (!e() && t[n] === e) {
				t.splice(n--, 1)
			}
		}
		if (!t.length) {
			v.fx.stop()
		}
		Ot = i
	};
	v.fx.timer = function(e) {
		if (e() && v.timers.push(e) && !Wt) {
			Wt = setInterval(v.fx.tick, v.fx.interval)
		}
	};
	v.fx.interval = 13;
	v.fx.stop = function() {
		clearInterval(Wt);
		Wt = null
	};
	v.fx.speeds = {
		slow: 600,
		fast: 200,
		_default: 400
	};
	v.fx.step = {};
	if (v.expr && v.expr.filters) {
		v.expr.filters.animated = function(e) {
			return v.grep(v.timers, function(t) {
				return e === t.elem
			}).length
		}
	}
	var tn = /^(?:body|html)$/i;
	v.fn.offset = function(e) {
		if (arguments.length) {
			return e === i ? this : this.each(function(t) {
				v.offset.setOffset(this, e, t)
			})
		}
		var t, n, r, s, a, o, l, c = {
				top: 0,
				left: 0
			},
			u = this[0],
			f = u && u.ownerDocument;
		if (!f) {
			return
		}
		if ((n = f.body) === u) {
			return v.offset.bodyOffset(u)
		}
		t = f.documentElement;
		if (!v.contains(t, u)) {
			return c
		}
		if (typeof u.getBoundingClientRect !== "undefined") {
			c = u.getBoundingClientRect()
		}
		r = nn(f);
		s = t.clientTop || n.clientTop || 0;
		a = t.clientLeft || n.clientLeft || 0;
		o = r.pageYOffset || t.scrollTop;
		l = r.pageXOffset || t.scrollLeft;
		return {
			top: c.top + o - s,
			left: c.left + l - a
		}
	};
	v.offset = {
		bodyOffset: function(e) {
			var t = e.offsetTop,
				n = e.offsetLeft;
			if (v.support.doesNotIncludeMarginInBodyOffset) {
				t += parseFloat(v.css(e, "marginTop")) || 0;
				n += parseFloat(v.css(e, "marginLeft")) || 0
			}
			return {
				top: t,
				left: n
			}
		},
		setOffset: function(e, t, n) {
			var i = v.css(e, "position");
			if (i === "static") {
				e.style.position = "relative"
			}
			var r = v(e),
				s = r.offset(),
				a = v.css(e, "top"),
				o = v.css(e, "left"),
				l = (i === "absolute" || i === "fixed") && v.inArray("auto", [a, o]) > -1,
				c = {},
				u = {},
				f, d;
			if (l) {
				u = r.position();
				f = u.top;
				d = u.left
			} else {
				f = parseFloat(a) || 0;
				d = parseFloat(o) || 0
			} if (v.isFunction(t)) {
				t = t.call(e, n, s)
			}
			if (t.top != null) {
				c.top = t.top - s.top + f
			}
			if (t.left != null) {
				c.left = t.left - s.left + d
			}
			if ("using" in t) {
				t.using.call(e, c)
			} else {
				r.css(c)
			}
		}
	};
	v.fn.extend({
		position: function() {
			if (!this[0]) {
				return
			}
			var e = this[0],
				t = this.offsetParent(),
				n = this.offset(),
				i = tn.test(t[0].nodeName) ? {
					top: 0,
					left: 0
				} : t.offset();
			n.top -= parseFloat(v.css(e, "marginTop")) || 0;
			n.left -= parseFloat(v.css(e, "marginLeft")) || 0;
			i.top += parseFloat(v.css(t[0], "borderTopWidth")) || 0;
			i.left += parseFloat(v.css(t[0], "borderLeftWidth")) || 0;
			return {
				top: n.top - i.top,
				left: n.left - i.left
			}
		},
		offsetParent: function() {
			return this.map(function() {
				var e = this.offsetParent || a.body;
				while (e && (!tn.test(e.nodeName) && v.css(e, "position") === "static")) {
					e = e.offsetParent
				}
				return e || a.body
			})
		}
	});
	v.each({
		scrollLeft: "pageXOffset",
		scrollTop: "pageYOffset"
	}, function(e, t) {
		var n = /Y/.test(t);
		v.fn[e] = function(r) {
			return v.access(this, function(e, r, s) {
				var a = nn(e);
				if (s === i) {
					return a ? t in a ? a[t] : a.document.documentElement[r] : e[r]
				}
				if (a) {
					a.scrollTo(!n ? s : v(a).scrollLeft(), n ? s : v(a).scrollTop())
				} else {
					e[r] = s
				}
			}, e, r, arguments.length, null)
		}
	});

	function nn(e) {
		return v.isWindow(e) ? e : e.nodeType === 9 ? e.defaultView || e.parentWindow : false
	}
	v.each({
		Height: "height",
		Width: "width"
	}, function(e, t) {
		v.each({
			padding: "inner" + e,
			content: t,
			"": "outer" + e
		}, function(n, r) {
			v.fn[r] = function(r, s) {
				var a = arguments.length && (n || typeof r !== "boolean"),
					o = n || (r === true || s === true ? "margin" : "border");
				return v.access(this, function(t, n, r) {
					var s;
					if (v.isWindow(t)) {
						return t.document.documentElement["client" + e]
					}
					if (t.nodeType === 9) {
						s = t.documentElement;
						return Math.max(t.body["scroll" + e], s["scroll" + e], t.body["offset" + e], s["offset" + e], s["client" + e])
					}
					return r === i ? v.css(t, n, r, o) : v.style(t, n, r, o)
				}, t, a ? r : i, a, null)
			}
		})
	});
	if (typeof define === "function" && define.amd && define.amd.jQuery) {
		define("jquery", [], function() {
			return v
		})
	}
	return v
});
define("basic/oop/Class", function(e, t, n) {
	var i = false;
	var r = /xyz/.test(function() {
		xyz
	}) ? /\b_super\b/ : /.*/;

	function s(e, t) {
		return function() {
			var n = this._super;
			this._super = e;
			var i = t.apply(this, arguments);
			this._super = n;
			return i
		}
	}
	var a = function() {};
	a.extend = function(e) {
		var t = this.prototype;
		i = true;
		var n = new this;
		i = false;
		for (var a in e) {
			n[a] = typeof e[a] == "function" && typeof t[a] == "function" && r.test(e[a]) ? s(t[a], e[a]) : e[a]
		}

		function o() {
			if (!i && this.init) this.init.apply(this, arguments)
		}
		o.prototype = n;
		o.constructor = o;
		o.extend = arguments.callee;
		return o
	};
	return a
});
define("common/Feedback/Feedback", function(e, t, n) {
	var i = e("basic/jquery");
	var r = e("basic/Util");
	var s = e("common/Win");
	var a = e("common/Validate");
	var o = e("common/NaviTip");
	var l = e("common/Feedback/Tpl");

	function c() {
		var e = this;
		i(r.formatTemplate(l.message)).appendTo("body");
		this.win = new s(i("#feedbackBox"), true, "display", true);
		this.init()
	}
	c.prototype.init = function() {
		var e = i("#messageInfo"),
			t = i("#mesError"),
			n = i("#mesCountWraper"),
			s = i("#mesErrorCountWraper"),
			l = i("#mesCount"),
			c = i("#mesErrorCount");
		var u = this;
		var f = new a.Validate("#feedbackForm", function() {
			i.post("/message/feedback", {
				content: e.val()
			}, function(e) {
				if (e.status == 1) {
					u.hide();
					o.show("\u6210\u529f\u63d0\u4ea4\u53cd\u9988\u4fe1\u606f\uff0c\u8c22\u8c22\u60a8\u7684\u53c2\u4e0e")
				} else {
					u.hide();
					o.show("\u6d88\u606f\u53d1\u9001\u5931\u8d25\uff0c\u8bf7\u7a0d\u540e\u518d\u8bd5", "error")
				}
			});
			return false
		});
		var d = new a.ValidateItem({
			node: e,
			check: function() {
				var e = i.trim(this.node.val());
				var a = r.getLength(e);
				if (e == "" || a > 163) {
					t.show();
					s.hide();
					n.hide();
					this.node.focus();
					return false
				} else {
					t.hide();
					return true
				}
			}
		});
		f.add(d);
		this.bindCheck(e, n, s, l, c, t);
		i("#messageSubmit").click(function() {
			i("#feedbackForm").submit()
		})
	};
	c.prototype.bindCheck = function(e, t, n, i, s, a) {
		var o = this;

		function l() {
			var o = 163 - r.getLength(e.val());
			if (o >= 0) {
				t.show();
				n.hide();
				i.html(o)
			} else {
				t.hide();
				n.show();
				s.html(Math.abs(o))
			}
			a.hide()
		}
		e.bind("keyup", function() {
			if (o.countTimer) {
				clearTimeout(o.countTimer);
				o.countTimer = setTimeout(function() {
					l()
				}, 100)
			} else {
				o.countTimer = setTimeout(function() {
					l()
				}, 100)
			}
		})
	};
	c.prototype.show = function() {
		this.win.show()
	};
	c.prototype.hide = function() {
		this.win.hide()
	};
	i("body").delegate(".js-feedback", "click", function() {
		(new c).show();
		return false
	})
});
define("common/Feedback/Tpl", function(e, t, n) {
	var i = e("basic/jquery");
	return {
		message: '<div class="dialogLayer msgdialogLayer" id="feedbackBox">                     <div class="dialogLayer-hd">                        <a class="png24 btn-close js-close" href="javascript:" title="\u5173\u95ed"></a>                     </div>                     <div class="dialogLayer-bd">   	                  <form id="feedbackForm">                           <h3>                              <strong>\u610f\u89c1\u53cd\u9988</strong>                           </h3>      	                  <p><textarea id="messageInfo"></textarea></p>                           <p class="msg-send-info">                              <span id="mesCountWraper" class="fl">\u8fd8\u80fd\u8f93\u5165<em id="mesCount">163</em>\u5b57</span>                              <span class="fl" style="display:none" id="mesErrorCountWraper">\u5df2\u7ecf\u8d85\u51fa<em class="cDRed" id="mesErrorCount"></em>\u5b57</span>                              <span class="fl cDRed" id="mesError" style="display:none">\u53cd\u9988\u5185\u5bb9\u57281\u5230163\u5b57\u4e4b\u95f4</span>                              <a class="orange-btnS" href="javascript:;" id="messageSubmit">\u63d0\u4ea4</a>                           </p>   	                  </form>                     </div>                  </div>'
	}
});
define("common/FloatBox/FloatBox", function(e, t, n) {
	var i = e("basic/jquery");
	var r = e("basic/Util");
	var s = e("common/FloatBox/Tpl");
	var a = e("message/Message");
	var o = e("common/Log/Logger");

	function l(e) {
		this.options = {
			wraper: i(document.body),
			scrollNode: null
		};
		if (e) {
			for (var t in e) {
				this.options[t] = e[t]
			}
		}
		this.actionType = 1;
		this.action = function(e) {};
		this.dataPool = {};
		this.delay = 400;
		this.left = 0;
		this.top = 0;
		this.docWidth = this.options.wraper.width(), this.docPos = this.options.wraper.offset(), this.entertimer = setTimeout(function() {}, this.delay);
		this.outtimer = setTimeout(function() {}, this.delay);
		this.wraper = document.createElement("div");
		this.wraper.className = "cardLayer";
		this.wraper.style.visibility = "hidden";
		this.inner = document.createElement("div");
		this.inner.className = "cardLayer-inner";
		this.arrow = document.createElement("em");
		this.content = document.createElement("div");
		this.wraper.appendChild(this.inner);
		this.inner.appendChild(this.arrow);
		this.inner.appendChild(this.content);
		document.body.insertBefore(this.wraper, document.body.firstChild);
		var n = this;
		i(this.wraper).delegate(".js-send", "click", function(e) {
			if (CONFIG.islogined) {
				var t = r.getLinkParam(this).toString(),
					s = this.getAttribute("username");
				if (n.actionType == 1) {
					new a(t, s).show()
				} else {
					new a(t, s, function(e) {
						n.action(e)
					}).show()
				}
				o.triggerLog(i(e.currentTarget), {
					method: "mail"
				})
			} else {
				r.login()
			}
		});
		i(this.wraper).delegate(".js-cardfollow", "click", function(e) {
			var t = r.getLinkParam(this).toString(),
				s = i(this),
				a = n.dataPool[t].followedCount;
			if (CONFIG.islogined) {
				r.follow(t, function() {
					s.hide();
					i(n.wraper).find(".js-cardfollowed").show();
					n.dataPool[t].isFollowing = true;
					n.dataPool[t].followedCount = a + 1
				});
				o.triggerLog(i(e.currentTarget), {
					method: "follow"
				})
			} else {
				r.login()
			}
		});
		i(this.wraper).delegate(".js-carddefollow", "click", function(e) {
			var t = r.getLinkParam(this).toString(),
				s = i(this),
				a = n.dataPool[t].followedCount;
			if (CONFIG.islogined) {
				r.defollow(t, function() {
					s.parent().hide();
					i(n.wraper).find(".js-cardfollow").show();
					n.dataPool[t].isFollowing = false;
					n.dataPool[t].followedCount = a - 1
				});
				o.triggerLog(i(e.currentTarget), {
					method: "unfollow"
				})
			} else {
				r.login()
			}
		});
		var n = this;
		i(n.wraper).hover(function() {
			clearTimeout(n.outtimer);
			n.entertimer = setTimeout(function() {
				n.show()
			}, n.delay)
		}, function() {
			clearTimeout(n.outtimer);
			n.outtimer = setTimeout(function() {
				n.hide()
			}, n.delay)
		})
	}
	l.prototype.hover = function(e, t) {
		var n = this;
		clearTimeout(n.entertimer);
		n.entertimer = setTimeout(function() {
			n.getData(t, e)
		}, n.delay)
	};
	l.prototype.out = function() {
		var e = this;
		clearTimeout(e.entertimer);
		e.outtimer = setTimeout(function() {
			e.hide()
		}, e.delay)
	};
	l.prototype.cut = function(e) {
		var e = e || "\u8fd9\u5bb6\u4f19\u5f88\u61d2\uff0c\u4ec0\u4e48\u90fd\u6ca1\u5199";
		if (r.getLength(e) > 25) {
			return r.cutString(e, 0, 25) + ".."
		}
		return r.encodeSpecialHtmlChar(e)
	};
	l.prototype.getData = function(e, t) {
		var n = this;
		if (!n.dataPool[e]) {
			i(n.inner).addClass("cardLayer-state");
			n.content.innerHTML = "\u8f7d\u5165\u4e2d...";
			i.get("/gethomeUserCard", {
				userId: e
			}, function(i) {
				if (i.status == 1) {
					var s = false,
						a = false;
					if (CONFIG.islogined) {
						if (CONFIG.userId == i.userId) {
							s = true
						}
						a = i.isFollow
					}
					var i = {
						anchorLevel: i.userCard.anchorLevel,
						anchorLevelName: r.anchor[i.userCard.anchorLevel],
						is_Anchor: i.userCard.is_Anchor,
						userLevel: i.userCard.userLevel,
						userLevelName: r.wealth[i.userCard.userLevel],
						familyId: i.userCard.familyId,
						nickName: i.userCard.nickName,
						intro: n.cut(i.userCard.intro),
						followedCount: i.userCard.followedCount,
						avatar: r.getImageUrl(i.userCard.avatar, 50),
						roomId: i.userCard.roomId,
						live: i.userCard.live,
						province: i.userCard.province,
						city: i.userCard.city,
						area: i.userCard.area,
						age: i.userCard.age,
						star: i.userCard.star,
						familyBadge: i.userCard.familyBadge,
						familyBadgeName: i.userCard.familyBadgeName,
						familytrueId: i.userCard.familyWrapInfo ? i.userCard.familyWrapInfo.familyId : 0,
						badgeImageUrl: i.userCard.badgeImageUrl,
						boboUser: i.userCard.boboUser,
						boboQianYueUser: i.userCard.boboQianYueUser,
						vipUser: i.userCard.vipUser,
						basicVipUser: i.userCard.basicVipUser,
						sex: i.userCard.sex,
						userId: i.userId,
						isSelf: s,
						isFollowing: a,
						badgeList: i.userCard.badgeList,
						badgeUrlList: i.userCard.badgeUrlList,
						userEventIcons: i.userEventIcons,
						userNum: i.userCard.userNum
					};
					n.dataPool[e] = i;
					n.render(i, t)
				} else {
					n.render("nouser", t)
				}
			})
		} else {
			var s = n.dataPool[e];
			n.render(s, t)
		}
	};
	l.prototype.render = function(e, t) {
		var n = this;
		i(n.inner).closest(".cardLayer").addClass("js-userFloatBox");
		if (e.length) {
			i(n.inner).addClass("cardLayer-state");
			n.content.innerHTML = "\u65e0\u6b64\u7528\u6237"
		} else {
			i(n.inner).removeClass("cardLayer-state");
			n.content.innerHTML = r.formatTemplate(s.FloatBox, e)
		}
		n.setPos(t)
	};
	l.prototype.setPos = function(e) {
		var t = this,
			n = i(t.wraper).height() + 10,
			r = 364,
			s = t.getStrollTop(),
			a = i(e).width(),
			o = i(e).height();
		var l = 55 - Math.floor(a / 2);
		var c = 43 - Math.floor(o / 2);
		var u = i(e).offset();
		if (u.top - s - n > 0) {
			t.top = u.top - n;
			t.addClassName("icon-card-bottom")
		} else {
			t.top = u.top + 10 + o;
			t.addClassName("icon-card-top")
		} if (t.docWidth + t.docPos.left - u.left - r > 0) {
			t.left = u.left - l
		} else {
			t.left = u.left - r - l;
			t.top = u.top - c;
			t.addClassName("icon-card-right")
		}
		t.options.scrollNode && (t.top -= t.options.scrollNode.scrollTop);
		t.wraper.style.left = t.left + "px";
		t.wraper.style.top = t.top + "px";
		t.show()
	};
	l.prototype.hide = function() {
		this.wraper.style.visibility = "hidden"
	};
	l.prototype.getLeft = function() {
		return this.left
	};
	l.prototype.show = function() {
		this.wraper.style.visibility = "visible"
	};
	l.prototype.addClassName = function(e) {
		i(this.arrow).addClass("png24");
		i(this.arrow).removeClass("icon-card-bottom").removeClass("icon-card-top").removeClass("icon-card-right").addClass(e)
	};
	l.prototype.getStrollTop = function() {
		oDocument = document;
		return Math.max(oDocument.documentElement.scrollTop, oDocument.body.scrollTop)
	};
	var c = new l;
	var u = i(c.wraper);

	function f(e, t, n, i) {
		var i = i || function() {};
		e.animate({
			left: t
		}, {
			duration: n,
			easing: "linear",
			complete: function() {
				i()
			}
		})
	}
	i("body").delegate(".js-box", "mouseover", function() {
		var e = this.getAttribute("userId");
		c.actionType = 1;
		c.hover(this, e)
	});
	i("body").delegate(".js-box", "mouseout", function() {
		var e = this.getAttribute("userId");
		c.actionType = 1;
		c.out(this, e)
	});
	i("body").delegate(".js-box", "click", function() {
		window.clicktimer && clearTimeout(window.clicktimer);
		window.clicktimer = setTimeout(function() {
			var e = c.getLeft(),
				t = e - 30,
				n = e + 30;
			f(u, t, 30, function() {
				f(u, n, 60, function() {
					f(u, e, 30)
				})
			})
		}, 420)
	});
	return l
});
define("common/FloatBox/Tpl", function(e, t, n) {
	return {
		FloatBox: '<div class="cardLayer-hd">		            <div class="cardLayer-user clearfix">			         <a class="fl avatar-m" href="javascript:;"><img src="<%=avatar%>"><em></em></a>			         <span class="card-wealth"><em title="<%=userLevelName%>" class="png24 medal-wealth<%=userLevel%>"></em></span>			         <%if(familytrueId>0){%>			         <span class="card-family"><a class="png24" href="/family/<%=familytrueId%>" data-method="family"><img src="<%=badgeImageUrl%>"/></a></span>			         <%}%>			         <h5><%=nickName%>			         <%if(is_Anchor){%>			          <em title="<%=anchorLevelName%>" class="card-anchor png24 medal-anchor<%=anchorLevel%>"></em>			         <%}%>			         </h5>			         <p class="card-list">					      <%if(boboQianYueUser){%><em class="png24 icon-dujia" title="\u72ec\u5bb6\u7b7e\u7ea6\u4e3b\u64ad"></em><%}%>					      <%if(vipUser){%><em class="png24 icon-member" title="\u8d85\u7ea7\u4f1a\u5458"></em><%}%>					      <%if(basicVipUser){%><em class="png24 icon-member2" title="\u57fa\u7840\u4f1a\u5458"></em><%}%>					      <%if(boboUser){%><em class="png24 icon-bbk" title="\u6ce2\u6ce2\u5361\u7528\u6237"></em><%}%>			         <%if(badgeList){%>				      	<%for(var i = 0; i < userEventIcons.length; i++){%>				      	<a href="javascript:;"><img class="png24" src="<%=userEventIcons[i].iconUrl%>"/></a>				      	<%}%>			      	<%}%>			      	</p>			         <%if(badgeUrlList){%>			      	<p class="card-list">				      	<%for(var i = 0; i < badgeUrlList.length; i++){%>				      	<em class="png24" title="\u793c\u7269\u5468\u661f\u52cb\u7ae0"><img src="<%=badgeUrlList[i]%>"></em>				      	<%}%>			      	</p>			      	<%}%>			      	</div>			      	<div class="cardLayer-info">				      	<p><%=sex%><i>|</i><%=age%><i>|</i><%=star%><i>|</i><%=province%>&nbsp;<%if(province=="\u5317\u4eac\u5e02"||province=="\u4e0a\u6d77\u5e02"){%><%=area%><%}else{%><%=city%><%}%><%if(is_Anchor){%><i>|</i><em class="png24 icon-personal"></em><a class="cOrange" href="/i/<%=roomId%>" target="_blank">TA\u7684\u7a7a\u95f4</a><%}%></p>				      	<p><%=intro%></p>			      	</div>		         </div>	            <div class="cardLayer-bd">		            	<span class="card-follow-info">			            <%if(isSelf){%>			             <span class="card-followed">			             <em></em>\u6211\u81ea\u5df1(<%=followedCount%>)			            </span>			            <%}else{%>			             <%if(isFollowing){%>			               <span class="card-followed js-cardfollowed">			               <em class="png24 icon-rightG"></em>			                \u5df2\u5173\u6ce8(<%=followedCount%>)|<a href="javascript://<%=userId%>" class="js-carddefollow" followcount="<%=followedCount%>">\u53d6\u6d88</a>			               </span>			               <a href="javascript://<%=userId%>" class="js-cardfollow" followcount="<%=followedCount%>" style="display:none">			                <%var count=followedCount-1%>			                <em class="png24 icon-add"></em>\u5173\u6ce8(<%=count%>)			               </a>			             <%}else{%>			              <span class="card-followed js-cardfollowed" style="display:none">			               <em class="png24 icon-rightG"></em>			                <%var count=followedCount+1%>			                \u5df2\u5173\u6ce8(<%=count%>)|<a href="javascript://<%=userId%>" class="js-carddefollow">\u53d6\u6d88</a>			               </span>			             	<a href="javascript://<%=userId%>" class="js-cardfollow">			                <em class="png24 icon-add"></em>\u5173\u6ce8(<%=followedCount%>)			               </a>			             <%}%>			            <%}%>			            <%if(!isSelf){%>			             <a href="javascript://<%=userId%>" class="js-send" username="<%=nickName%>" style="margin-right:2px;"><em class="png24 icon-mailB"></em>\u53d1\u79c1\u4fe1</a>			            <%}%>			            </span>			            <%if(is_Anchor){%>			            <a class="orange-btn" href="/<%=roomId%>" target="_blank" data-method="goin">\u8fdb\u5165\u623f\u95f4</a>			            <%}%>	            </div>'
	}
});
define("common/FloatWin/FloatWin", function(e, t, n) {
	var i = e("basic/jquery");
	var r = e("basic/Util");
	var s = e("common/Win");
	var a = e("common/FloatWin/Tpl");

	function o(e) {
		var t = this;
		var n = {
			content: "",
			sub: "",
			buttonText1: "\u786e\u5b9a",
			buttonText2: "\u53d6\u6d88",
			confirmFunc: function() {},
			cancelFunc: function() {}
		};
		for (var o in e) {
			n[o] = e[o]
		}
		t.opts = n;
		this.$elem = i(r.formatTemplate(a.win1, n)).appendTo("body");
		this.win = new s(this.$elem, true, "display", true);
		this.$elem.find(".js-confirm").click(function(e) {
			t.opts.confirmFunc(e)
		});
		this.$elem.find(".js-cancel").click(function() {
			t.opts.cancelFunc()
		})
	}
	o.prototype.show = function() {
		this.win.show()
	};
	o.prototype.hide = function() {
		this.win.hide()
	};

	function l(e) {
		var t = this;
		var n = {
			content: "",
			sub: "",
			buttonText1: "\u786e\u5b9a",
			confirmFunc: function() {},
			showButton: false,
			notAutoHide: false
		};
		for (var o in e) {
			n[o] = e[o]
		}
		t.opts = n;
		this.$elem = i(r.formatTemplate(a.win2, n)).appendTo("body");
		this.win = new s(this.$elem, true, "display", true);
		this.$elem.find(".js-confirm").click(function(e) {
			t.opts.confirmFunc(e)
		})
	}
	l.prototype.show = function() {
		var e = this;
		e.win.show();
		if (!e.opts.showButton && !e.opts.notAutoHide) {
			setTimeout(function() {
				e.hide()
			}, 1e3)
		}
	};
	l.prototype.hide = function() {
		this.win.hide()
	};

	function c(e) {
		var t = this;
		var n = {
			title: "\u8f93\u5165\u9a8c\u8bc1\u7801",
			url: "",
			confirmFunc: function(e, t) {}
		};
		for (var o in e) {
			n[o] = e[o]
		}
		t.opts = n;
		this.$elem = i(r.formatTemplate(a.win3, n)).appendTo("body");
		this.win = new s(this.$elem, true, "display", true);
		var l = i("#winDialogLayer");
		var c = l.find(".js-codeurl"),
			u = l.find(".js-codefresh"),
			f = l.find(".js-codeinput"),
			d = l.find(".js-codeerror");
		u.click(function() {
			c.attr("src", n.url + "&timestamp=" + (new Date).getTime());
			d.hide();
			return false
		});
		if (i.browser.msie && parseInt(i.browser.version, 10) < 7) {
			setTimeout(function() {
				u.click()
			}, 100)
		}
		l.find(".js-confirm").click(function() {
			t.opts.confirmFunc(f, d, c)
		});
		f.keydown(function(e) {
			if (e.keyCode == 13) {
				t.opts.confirmFunc(f, d, c);
				return false
			}
		})
	}
	c.prototype.show = function() {
		this.win.show()
	};
	c.prototype.hide = function() {
		this.win.hide()
	};

	function u(e) {
		var t = this;
		var n = {
			preRender: function() {
				return {
					content: "",
					title: ""
				}
			},
			postRender: function() {}
		};
		for (var o in e) {
			n[o] = e[o]
		}
		t.opts = n;
		var l = t.opts.preRender();
		var c = t.opts.tpl || a.win;
		t.$elem = i(r.formatTemplate(c, l)).appendTo("body").hide();
		t.opts.postRender.apply(null, [t.$elem]);
		this.win = new s(t.$elem, true, "display", true)
	}
	u.prototype.show = function() {
		this.win.show()
	};
	u.prototype.hide = function() {
		this.win.hide();
		this.$elem && this.$elem.detach()
	};
	var f = null;

	function d(e, t) {
		if (f !== null) {
			f.hide()
		}
		f = new e(t);
		return f
	}
	return {
		ConfirmsWin: o,
		AlertWin: l,
		CheckCodeWin: c,
		PopWin: function(e) {
			return d(u, e)
		}
	}
});
define("common/FloatWin/Tpl", function(e, t, n) {
	return {
		win1: '<div class="dialogLayer popWidth"  id="winDialogLayer">               <div class="dialogLayer-hd">                  <a class="png24 btn-close js-close" href="javascript:;" title="\u5173\u95ed"></a>               </div>               <div class="dialogLayer-bd">   	            <h3><%=content%></h3>   	            <p class="cGray"><%=sub%></p>   	            <p><a class="orange-btnS js-confirm" href="javascript:;"><%=buttonText1%></a><a class="gray-btnS js-close js-cancel" href="javascript:;"><%=buttonText2%></a></p>               </div>            </div>',
		win2: '<div class="dialogLayer popWidth" id="winDialogLayer" style="_position:absolute">               <div class="dialogLayer-hd">                  <a class="png24 btn-close js-close" href="javascript:;" title="\u5173\u95ed"></a>               </div>               <div class="dialogLayer-bd js-bd">   	            <h3><%=content%></h3>   	            <p class="cGray"><%=sub%></p>   	            <%if(showButton){%>   	            <p><a class="orange-btnS js-close js-confirm" href="javascript:;"><%=buttonText1%></a></p>   	            <%}%>               </div>            </div>',
		win3: '<div class="dialogLayer popWidth" id="winDialogLayer" style="_position:absolute">               <div class="dialogLayer-hd">                  <a class="png24 btn-close js-close" href="javascript:;" title="\u5173\u95ed"></a>               </div>               <div class="dialogLayer-bd">   	            <h3><%=title%></h3>   	            <div>   	             <img src="<%=url%>" class="js-codeurl"/><a href="javascript:;" class="js-codefresh">\u70b9\u6b64\u5237\u65b0</a>   	             <div>   	             <input type="text" class="js-codeinput" style="width:100px;height:20px;border:1px solid #ccc; margin-bottom:10px;margin-top:10px;"/>   	             <span class="js-codeerror" style="color:#F44103;display:none;_position:relative;_top:-10px"> \u9a8c\u8bc1\u7801\u9519\u8bef</span>   	             </div>   	            </div>   	            <p><a class="orange-btnS js-confirm" href="javascript:;">\u786e\u5b9a</a></p>               </div>            </div>',
		win: '<div class="dialogLayer">               <div class="dialogLayer-hd">                  <a class="png24 btn-close js-close" href="javascript:;" title="\u5173\u95ed"></a>               </div>               <div class="dialogLayer-bd clearfix js-bd">   	            <%=content%>               </div>            </div>'
	}
});
define("common/HoverSelect", function(e, t, n) {
	var i = e("basic/jquery");
	var r = e("basic/oop/Class");
	var s = r.extend({
		mouseInTimer: null,
		mouseOutTimer: null,
		DEFAULT: {
			$hoverSrc: "",
			$hoverTarget: "",
			delay: 300,
			enterFunc: function() {},
			leaveFunc: function() {}
		},
		init: function(e) {
			this.config = i.extend({}, this.DEFAULT, e);
			this.initHover()
		},
		initHover: function() {
			this.config.$hoverSrc.unbind("hover.select").bind("hover.select", i.proxy(function(e) {
				if (e.type === "mouseenter") {
					if (this.config.$hoverTarget.is(":visible")) {
						this.mouseOutTimer && clearTimeout(this.mouseOutTimer)
					}
					this.mouseInTimer = setTimeout(i.proxy(function() {
						this.config.$hoverTarget.show();
						this.config.enterFunc && this.config.enterFunc()
					}, this), this.config.delay)
				} else {
					this.mouseInTimer && clearTimeout(this.mouseInTimer);
					this.mouseOutTimer = setTimeout(i.proxy(function() {
						this.config.$hoverTarget.hide();
						this.config.leaveFunc && this.config.leaveFunc()
					}, this), this.config.delay)
				}
			}, this));
			this.config.$hoverTarget.unbind("hover.select").bind("hover.select", i.proxy(function(e) {
				if (e.type === "mouseenter") {
					this.mouseOutTimer && clearTimeout(this.mouseOutTimer)
				} else {
					this.mouseOutTimer = setTimeout(i.proxy(function() {
						this.config.$hoverTarget.hide();
						this.config.leaveFunc && this.config.leaveFunc()
					}, this), this.config.delay)
				}
			}, this))
		}
	});
	return s
});
define("common/Log/Log", function(e, t, n) {
	var i = e("basic/jquery");
	var r = e("common/Log/Logger");
	i(document).delegate("a[href]", "click", function(e) {
		var t = i(e.currentTarget);
		var n = t.attr("href");
		if (n.indexOf("/urlcheck?p=") !== -1 || t.data("unlog")) {
			return
		}
		var s = t.data("method");
		s = typeof s !== "undefined" ? s : "click";
		r.triggerLog(t, {
			method: s
		})
	})
});
define("common/Log/Logger", function(e, t, n) {
	var i = e("basic/jquery");
	var r = e("basic/Util");
	var s = {
		home: "pHome",
		rank: "pRank",
		family: "pFamily",
		shop: "pShop",
		search: "pSearch"
	};
	var a = {};
	var o = i.extend({}, s, a);
	var l = function(e) {
		var t = i(e).first();
		if (t[0] == window) {
			return "window"
		} else {
			var n = i.map(t.parents().andSelf(), function(e) {
				return i(e).data("keyfrom") || undefined
			});
			var r = o[CONFIG["pageName"]];
			r && n.unshift(r);
			return n.join(".")
		}
	};
	var c = function(e) {
		var t = i.param(e, true);
		(new Image).src = "/perform.do?" + t
	};
	var u = function(e) {
		return Math.floor(e * (Math.random() % 1))
	};
	var f = [new Image, new Image, new Image, new Image, new Image];
	var d = function(e) {
		if (typeof e === "undefined") {
			throw "no analytics address";
			return
		}
		var t = f[u(5)];
		if (e.match(/^http:\/\/.+/)) {
			t.src = e
		} else {
			t.src = "http://" + location.host + (/^\//.test(e) ? e : "/" + e)
		}
	};
	var p = function(e, t) {
		var n = (new Date).getTime();
		var r = "/page.do?random=" + n + "&keyfrom=" + l(e);
		if (t && i.isPlainObject(t)) {
			for (var s in t) {
				r += "&" + s + "=" + t[s]
			}
		} else {
			r += "&method=click"
		}
		d(r)
	};
	var h = function(e) {
		e.random = (new Date).getTime();
		var t = r.format(e);
		var n = "/page.do?" + t;
		i.get(n)
	};
	return {
		keyFrom: l,
		doLog: c,
		triggerLog: p,
		keyFromMap: o,
		doReuest: h
	}
});
define("common/Login/ImproUserInfo", function(e, t, n) {
	var i = e("basic/jquery");
	var r = e("basic/oop/Class");
	var s = e("common/FloatWin/FloatWin");
	var a = e("basic/Util");
	var o = e("common/Login/tpl");
	var l = r.extend({
		init: function() {
			this.$elem = null
		},
		bindEvent: function() {
			var t = e("common/Login/UserEdit");
			new t(false)
		},
		preRender: function() {
			var e = {
				nick: CONFIG["nick"],
				url: location.href
			};
			return {
				content: a.formatTemplate(o.improUserInfo, e)
			}
		},
		postRender: function(e) {
			e.addClass("loginLayer");
			this.$elem = e;
			this.$elem.find(".dialogLayer-hd").find(".js-close").removeClass("btn-close").addClass("btn-login-close");
			this.$elem.find(".dialogLayer-hd").append('<a class="receive-now" href="http://www.bobo.com/special/pcjf/" target="_blank"></a>')
		},
		show: function() {
			if (!CONFIG["isFirstLogin"] || CONFIG["pageName"] == "room") return;
			var e = this;
			setTimeout(function() {
				s.PopWin({
					preRender: i.proxy(e.preRender, e),
					postRender: i.proxy(e.postRender, e)
				}).show();
				e.bindEvent()
			}, 500)
		}
	});
	return l
});
define("common/Login/Login", function(e, t, n) {
	var i = e("basic/jquery");
	var r = e("basic/Util");
	var s = e("common/Log/Logger");
	i(document).delegate(".js-need-login", "click", function(e) {
		if (!CONFIG["islogined"]) {
			r.login();
			e.preventDefault();
			s.triggerLog(i(e.currentTarget))
		}
	})
});
define("common/Login/LoginBox", function(e, t, n) {
	var i = e("basic/jquery");
	var r = e("basic/oop/Class");
	var s = e("common/FloatWin/FloatWin");
	var a = e("basic/Util");
	var o = e("common/Login/tpl");
	var l = e("common/Login/LoginSuggest");
	var c = r.extend({
		init: function() {
			this.$elem = null
		},
		preRender: function() {
			var e = {
				url: a.encodeSpecialHtmlChar(window.location.href)
			};
			return {
				content: a.formatTemplate(o.login, e)
			}
		},
		postRender: function(e) {
			e.addClass("loginLayer");
			this.$elem = e;
			this.$inputElem = this.$elem.find("input:text");
			this.$passwordElem = this.$elem.find("input:password");
			this.$errorTip = this.$elem.find(".js-errorTip");
			this.$formElem = this.$elem.find("form");
			this.$formElem.delegate("input:text, input:password", "focus", i.proxy(this.hideError, this));
			this.$formElem.submit(i.proxy(this.onSubmit, this));
			this.$elem.find(".dialogLayer-hd").find(".js-close").removeClass("btn-close").addClass("btn-login-close");
			this.$elem.find(".dialogLayer-hd").append('<a class="receive-now" href="http://www.bobo.com/special/pcjf/" target="_blank"></a>')
		},
		show: function() {
			s.PopWin({
				preRender: i.proxy(this.preRender, this),
				postRender: i.proxy(this.postRender, this)
			}).show();
			this.onLoginSuggest();
			this.onPlaceHolder()
		},
		onLoginSuggest: function() {
			var e = document.getElementById("poplayer_username"),
				t = document.getElementById("poplayer_passport");
			new l(e, function() {
				i(t).focus()
			})
		},
		onPlaceHolder: function() {
			setTimeout(i.proxy(function() {
				this.$inputElem.focus()
			}, this), 0);
			if ("placeholder" in document.createElement("input")) {
				this.$formElem.find(".js-defaultText").hide();
				return
			}
			this.$formElem.delegate(".js-defaultText", "click", function() {
				i(this).prev("input").focus()
			}).delegate("input", "keyup", function() {
				if (i.trim(i(this).val()) === "") {
					i(this).next(".js-defaultText").show()
				} else {
					i(this).next(".js-defaultText").hide()
				}
			})
		},
		onCheckVal: function() {
			var e = i.trim(this.$inputElem.val());
			var t = this.$passwordElem.val();
			if (e === "") {
				this.showError("\u90ae\u7bb1\u5e10\u53f7\u4e0d\u80fd\u4e3a\u7a7a");
				return false
			} else if (!a.validateEmail(e)) {
				this.showError("\u90ae\u7bb1\u683c\u5f0f\u6709\u8bef");
				return false
			} else if (t === "") {
				this.showError("\u5bc6\u7801\u4e0d\u80fd\u4e3a\u7a7a");
				return false
			}
			this.hideError();
			return true
		},
		onSubmit: function(e) {
			if (!this.onCheckVal()) {
				e.preventDefault()
			}
		},
		showError: function(e) {
			this.$errorTip.show().find("span").text(e)
		},
		hideError: function() {
			this.$errorTip.hide()
		}
	});
	return c
});
define("common/Login/LoginSuggest", function(e, t, n) {
	var i = e("basic/jquery");
	var r = e("basic/Util");
	var s = 0,
		a = null;

	function o(e) {
		var t = 0,
			n = 0;
		while (e != document.body && e != null) {
			n += e.offsetLeft;
			t += e.offsetTop;
			e = e.offsetParent
		}
		return {
			x: n,
			y: t
		}
	}

	function l() {
		var e = this;
		var t = document.getElementById("passportusernamelist" + s);
		var n = o(e.usernameInputElement).x;
		var i = o(e.usernameInputElement).y;
		t.style.left = n + "px";
		t.style.top = i + e.usernameInputElement.offsetHeight + "px"
	}

	function c() {
		var e = this;
		var t = i("#passportusernamelist" + s);
		var n = i(e.usernameInputElement).position().left;
		var r = i(e.usernameInputElement).position().top;
		t.css({
			left: n
		});
		t.css({
			top: r + i(e.usernameInputElement).outerHeight(true)
		})
	}

	function u(e, t, n) {
		if (!arguments.length) {
			return
		}
		if (!a || e.id !== a.id) {
			s++;
			a = e
		}
		var i = this;
		i.constructor = arguments.callee;
		i.toscroll = false;
		if (n) {
			i.toscroll = true
		}
		i.usernameInputElement = false;
		i.usernameInputElementX = false;
		i.usernameInputElementY = false;
		i.usernameInputHeight = false;
		i.usernameListElement = false;
		i._initWidth = 0;
		i._runFuc = t;
		i.currentSelectIndex = -1;
		i.domainSelectElmentString = '<div style="padding:0px;"><table width="100%" cellspacing="0" cellpadding="0"><tbody><tr><td class="title" style="title" >\u8bf7\u9009\u62e9\u6216\u7ee7\u7eed\u8f93\u5165...</td></tr><tr><td><td /></tr></tbody></table></div><div style="display: none;"></div><div id="passport_111"></div>';
		i.domainSelectElement = false;
		i.domainArray = ["163.com", "126.com", "yeah.net", "qq.com", "vip.163.com", "vip.126.com", "188.com", "gmail.com", "sina.com", "hotmail.com"];
		i.helpDivString = '<div style="width:100%;" id="passport_helper_div"></div>';
		i.bind(e)
	}
	u.prototype = {
		bind: function(e) {
			var t = this;
			i(e).unbind();
			t.usernameInputElement = e;
			var n = o(t.usernameInputElement);
			t.usernameInputElementX = n.x;
			t.usernameInputElementY = n.y;
			t.handle();
			i(t.usernameInputElement).focus(function() {
				t._initWidth = t.usernameInputElement.offsetWidth - 2;
				t.domainSelectElement.style.width = t.usernameInputElement.offsetWidth - 2 + "px"
			})
		},
		handle: function() {
			var e = this;
			var t = "passportusernamelist" + s;
			if (!document.getElementById(t)) {
				var n = document.createElement("DIV");
				n.id = t;
				n.className = "domainSelector";
				n.style.display = "none";
				if (i.browser.msie && parseInt(i.browser.version, 10) < 7 || e.toscroll) {
					n.style.position = "absolute";
					i(e.usernameInputElement).parent().css({
						position: "relative"
					}).append(i(n))
				} else {
					n.style.position = "fixed";
					document.body.appendChild(n)
				}
				n.innerHTML = e.domainSelectElmentString
			}
			e.domainSelectElement = document.getElementById(t);
			e.usernameListElement = e.domainSelectElement.firstChild.firstChild.rows[1].firstChild;
			e.currentSelectIndex = 0;
			e.usernameInputElement.onblur = function() {
				e.doSelect.call(e)
			};
			try {
				this.usernameInputElement.addEventListener("keydown", e.keydownProc.bind(e), false);
				this.usernameInputElement.addEventListener("keyup", e.keyupProc.bind(e), false)
			} catch (r) {
				try {
					this.usernameInputElement.attachEvent("onkeydown", e.keydownProc.bind(e));
					this.usernameInputElement.attachEvent("onkeyup", e.keyupProc.bind(e))
				} catch (r) {}
			}
			if (i.browser.msie && parseInt(i.browser.version, 10) < 7 || e.toscroll) {
				c.call(e);
				return
			}
			l.call(e);
			if (i.browser.msie) {
				window.attachEvent("onresize", l.bind(e));
				if (parseInt(i.browser.version, 10) < 7 && e.noscroll) {
					e.doc = document.documentElement;
					e._tempTop = e.doc.scrollTop;
					e._tempScroll = e._scroll.bind(e);
					window.attachEvent("scroll", e._tempScroll)
				}
			} else {
				window.onresize = l.bind(e)
			}
		},
		preventEvent: function(e) {
			e.cancelBubble = true;
			e.returnValue = false;
			if (e.preventDefault) {
				e.preventDefault()
			}
			if (e.stopPropagation) {
				e.stopPropagation()
			}
		},
		_scroll: function() {
			var e = this;
			e.domainSelectElement.style.top = parseInt(e.domainSelectElement.style.top) + e.doc.scrollTop - e._tempTop;
			e._tempTop = e.doc.scrollTop
		},
		keyupProc: function(e) {
			var t = this;
			var n = e.keyCode;
			if (n == 13) {
				t.doSelect()
			} else if (n == 38 || n == 40) {
				t.clearFocus();
				if (n == 38) {
					t.upSelectIndex()
				} else {
					t.downSelectIndex()
				}
				t.setFocus()
			} else {
				t.changeUsernameSelect()
			}
		},
		keydownProc: function(e) {
			var t = this;
			var n = e.keyCode;
			if (n == 13) {
				if (i.trim(t.usernameInputElement.value) != "") t.preventEvent(e)
			} else if (n == 38 || n == 40) {
				t.preventEvent(e)
			}
		},
		clearFocus: function(e) {
			var t = this;
			var e = t.currentSelectIndex;
			try {
				var n = t.findTdElement(e);
				n.style.backgroundColor = "white"
			} catch (i) {}
		},
		findTdElement: function(e) {
			var t = this;
			try {
				var n = t.usernameListElement.firstChild.rows;
				for (var i = 0; i < n.length; ++i) {
					if (n[i].firstChild.idx == e) {
						return n[i].firstChild
					}
				}
			} catch (r) {}
			return false
		},
		upSelectIndex: function() {
			var e = this;
			var t = e.currentSelectIndex;
			if (e.usernameListElement.firstChild == null) {
				return
			}
			var n = e.usernameListElement.firstChild.rows;
			var i;
			for (i = 0; i < n.length; ++i) {
				if (n[i].firstChild.idx == t) {
					break
				}
			}
			if (i == 0) {
				e.currentSelectIndex = n.length - 1
			} else {
				e.currentSelectIndex = n[i - 1].firstChild.idx
			}
		},
		downSelectIndex: function() {
			var e = this;
			var t = e.currentSelectIndex;
			if (e.usernameListElement.firstChild == null) {
				return
			}
			var n = e.usernameListElement.firstChild.rows;
			var i = 0;
			for (; i < n.length; ++i) {
				if (n[i].firstChild.idx == t) {
					break
				}
			}
			if (i >= n.length - 1) {
				e.currentSelectIndex = n[0].firstChild.idx
			} else {
				e.currentSelectIndex = n[i + 1].firstChild.idx
			}
		},
		setFocus: function() {
			var e = this;
			var t = e.currentSelectIndex;
			try {
				var n = e.findTdElement(t);
				n.style.backgroundColor = "#D5F1FF"
			} catch (i) {}
		},
		changeUsernameSelect: function() {
			var e = this;
			var t = r.encodeSpecialHtmlChar(this.usernameInputElement.value);
			if (i.trim(t) == "") {
				this.domainSelectElement.style.display = "none"
			} else {
				var n = "",
					s = "";
				var a;
				if ((a = t.indexOf("@")) < 0) {
					n = t;
					s = ""
				} else {
					n = t.substr(0, a);
					s = t.substr(a + 1, t.length)
				}
				var o = r.countChars(n, 28);
				var c = [],
					u = [];
				if (s == "") {
					for (var f = 0; f < this.domainArray.length; ++f) {
						c.push(n + "@" + this.domainArray[f]);
						u.push(o + "@" + this.domainArray[f])
					}
				} else {
					for (var f = 0; f < this.domainArray.length; ++f) {
						if (this.domainArray[f].indexOf(s) == 0) {
							c.push(n + "@" + this.domainArray[f]);
							u.push(o + "@" + this.domainArray[f])
						}
					}
				} if (c.length > 0) {
					if (!e.toscroll) l.call(e);
					e.domainSelectElement.style.zIndex = "10000";
					e.domainSelectElement.style.backgroundColor = "white";
					e.domainSelectElement.style.display = "block";
					var d = document.createElement("TABLE");
					d.cellSpacing = 0;
					d.cellPadding = 3;
					var p = document.createElement("TBODY");
					d.appendChild(p);
					for (var f = 0; f < c.length; ++f) {
						var h = document.createElement("TR");
						var m = document.createElement("TD");
						m.nowrap = "true";
						m.align = "left";
						m.setAttribute("userName", c[f]);
						m.innerHTML = u[f];
						m.idx = f;
						m.onmouseover = function() {
							e.clearFocus();
							e.currentSelectIndex = this.idx;
							e.setFocus();
							this.style.cursor = "hand"
						};
						m.onmouseout = function() {};
						m.onclick = function() {
							e.doSelect()
						};
						h.appendChild(m);
						p.appendChild(h)
					}
					e.usernameListElement.innerHTML = "";
					e.usernameListElement.appendChild(d);
					var g = 0;
					for (var v = 0; v < c.length; ++v) {
						if (c[v].length > g) {
							g = c[v].length
						}
					}
					g = g * 7.4;
					if (g < e._initWidth) {
						g = e._initWidth
					}
					if (g > 306) g = 306;
					d.style.width = g + "px";
					this.domainSelectElement.style.width = d.style.width;
					this.setFocus();
					i(document).keydown(e.keydownListionEvent.bind(e))
				} else {
					this.domainSelectElement.style.display = "none";
					this.currentSelectIndex = -1
				}
			}
		},
		doSelect: function() {
			var e = this;
			e.domainSelectElement.style.display = "none";
			if (i.trim(e.usernameInputElement.value) == "") {
				return
			}
			var t = e.findTdElement(e.currentSelectIndex);
			if (t) {
				e.usernameInputElement.value = r.decodeSpecialHtmlChar(t.getAttribute("userName"))
			}
			e._runFuc()
		},
		keydownListionEvent: function(e) {
			var t = this;
			if (e.keyCode == 27) {
				this.domainSelectElement.style.display = "none";
				i(document).unbind("keydown", t.keydownListionEvent.bind(t))
			}
		}
	};
	return u
});
define("common/Login/UserEdit", function(e, t, n) {
	var i = e("basic/jquery");
	var r = e("basic/Util");
	var s = e("common/Validate");

	function a(e, t) {
		var n = 28;
		if (t == 2) {
			if (e % 4 == 0 && e % 100 != 0) {
				n = 29
			} else {
				n = 28
			}
		} else {
			switch (t) {
				case 1:
				case 3:
				case 5:
				case 7:
				case 8:
				case 10:
				case 12:
					n = 31;
					break;
				case 4:
				case 6:
				case 9:
				case 11:
					n = 30;
					break
			}
		}
		return n
	}

	function o(e) {
		this.isEditing = e;
		this.province = i("#province");
		this.provincehd = i("#provincehd");
		this.city = i("#city");
		this.cityhd = i("#cityhd");
		this.county = i("#county");
		this.countyhd = i("#countyhd");
		this.year = i("#year");
		this.yearhd = i("#yearhd");
		this.month = i("#month");
		this.monthhd = i("#monthhd");
		this.day = i("#day");
		this.dayhd = i("#dayhd");
		this.nickName = i("#nickName");
		this.nicknameTip = i("#nicknameTip");
		this.nicknameErrorTip = i("#nicknameErrorTip");
		this.nicknameIllegalTip = i("#nicknameIllegalTip");
		this.signature = i("#signature");
		this.signatureTip = i("#signatureTip");
		this.introIllegalTip = i("#introIllegalTip");
		this.sex = i("#sex");
		this.cancel = i("#cancel");
		this.init();
		this.bindLocation();
		this.bindBirth();
		this.bindValidate()
	}
	o.prototype.init = function() {
		var e = this;
		if (e.isEditing) {
			e.cancel.click(function() {
				window.location.href = "/user"
			})
		}
	};
	o.prototype.bindLocation = function() {
		var e = true,
			t = false;
		var n = [],
			r = this;
		i.get("/user/getprovinces.do", function(s) {
			for (var a = 0; a < s.provinces.length; a++) {
				n.push("<option value='" + s.provinces[a].pid + "'>" + s.provinces[a].province + "</option>")
			}
			setTimeout(function() {
				r.province.append(n.join("")).change(function() {
					var n = i(this).val(),
						s = [];
					if (n == "32" || n == "33" || n == "34" || n == "35") {
						r.city.hide();
						r.county.hide();
						return
					}
					r.city.show();
					r.county.show();
					t = true;
					i.get("/user/getcities.do", {
						pid: n
					}, function(n) {
						for (var a = 0; a < n.cities.length; a++) {
							s.push("<option value='" + n.cities[a].cid + "'>" + n.cities[a].city + "</option>")
						}
						r.city.find("option").remove();
						setTimeout(function() {
							r.city.append(s.join("")).change(function() {
								var t = i(this).val(),
									n = [];
								i.get("/user/getareas.do", {
									cid: t
								}, function(t) {
									for (var i = 0; i < t.areas.length; i++) {
										n.push("<option value='" + t.areas[i].aid + "'>" + t.areas[i].area + "</option>")
									}
									setTimeout(function() {
										r.county.find("option").remove();
										r.county.append(n.join(""));
										if (e) {
											setTimeout(function() {
												r.county.val(r.countyhd.val())
											}, 50);
											e = false
										}
									}, 50)
								})
							});
							if (e) {
								setTimeout(function() {
									r.city.val(r.cityhd.val())
								}, 50);
								setTimeout(function() {
									r.city.change()
								}, 100)
							}
							if (t) {
								setTimeout(function() {
									r.city.change()
								}, 50)
							}
							t = false
						}, 50)
					})
				});
				if (e) {
					setTimeout(function() {
						r.province.val(r.provincehd.val())
					}, 50);
					setTimeout(function() {
						r.province.change()
					}, 100)
				}
			}, 50)
		})
	};
	o.prototype.bindBirth = function() {
		var e = 1900,
			t = 2015,
			n = [],
			i = this;

		function r() {
			var e = [],
				t = parseInt(i.year.val()),
				n = parseInt(i.month.val());
			var r = a(t, n);
			i.day.find("option").remove();
			for (var s = 1; s <= r; s++) {
				var o = s;
				if (o < 10) {
					o = "0" + o
				}
				e.push("<option value='" + o + "'>" + o + "</option>")
			}
			setTimeout(function() {
				i.day.append(e.join(""));
				setTimeout(function() {
					i.day.val(i.dayhd.val())
				}, 50)
			}, 50)
		}
		for (var s = t; s > e; s--) {
			n.push("<option value='" + s + "'>" + s + "</option>")
		}
		setTimeout(function() {
			i.year.append(n.join("")).change(function() {
				r()
			});
			setTimeout(function() {
				i.year.val(i.yearhd.val())
			}, 50);
			setTimeout(function() {
				i.year.change()
			}, 100)
		}, 50);
		var o = [];
		for (var s = 1; s <= 12; s++) {
			var l = s;
			if (l < 10) {
				l = "0" + l
			}
			o.push("<option value='" + l + "'>" + l + "</option>")
		}
		setTimeout(function() {
			i.month.append(o.join("")).change(function() {
				r()
			});
			setTimeout(function() {
				i.month.val(i.monthhd.val())
			}, 50);
			setTimeout(function() {
				i.month.change()
			}, 100)
		}, 50)
	};
	o.prototype.bindValidate = function() {
		var e = new s.Validate("#userEdit"),
			t = this;
		var n = new s.ValidateItem({
			node: t.nickName,
			isAutoCheck: true,
			check: function() {
				var e = i.trim(this.node.val());
				var n = r.getLength(e),
					s = r.getleftLength(e);
				if (n > 10 || s < 2) {
					t.nicknameTip.show();
					t.nicknameErrorTip.hide();
					return false
				} else {
					t.nicknameTip.hide();
					return true
				}
			}
		});
		var a = new s.ValidateItem({
			node: t.signature,
			isAutoCheck: true,
			check: function() {
				var e = i.trim(this.node.val());
				var n = r.getLength(e);
				if (n > 30) {
					t.signatureTip.show();
					return false
				} else {
					t.signatureTip.hide();
					return true
				}
			}
		});
		var o = new s.ValidateItem({
			node: t.nickName,
			isAutoCheck: true,
			isVerified: true,
			check: function() {
				var e = i.trim(this.node.val()),
					n = this.node,
					s = this;
				var a = r.getLength(e),
					o = r.getleftLength(e);
				if (a > 10 || o < 2) {
					return
				}
				i.post("/user/checkNick.do", {
					nick: e
				}, function(e) {
					if (e.status == 0) {
						s.isVerified = false;
						t.nicknameErrorTip.show();
						t.nicknameTip.hide();
						t.nicknameIllegalTip.hide()
					} else if (e.status == 2) {
						s.isVerified = false;
						t.nicknameIllegalTip.show();
						t.nicknameErrorTip.hide();
						t.nicknameTip.hide()
					} else if (e.status == 1) {
						s.isVerified = true;
						t.nicknameErrorTip.hide();
						t.nicknameTip.hide();
						t.nicknameIllegalTip.hide()
					}
				})
			}
		});
		var l = new s.ValidateItem({
			node: t.signature,
			isAutoCheck: true,
			isVerified: true,
			check: function() {
				var e = i.trim(this.node.val()),
					n = this.node,
					s = this;
				var a = r.getLength(e);
				if (a > 30) {
					return
				}
				i.post("/check/checkMessage", {
					word: e
				}, function(e) {
					if (e.status == 2) {
						s.isVerified = false;
						t.introIllegalTip.show();
						t.signatureTip.hide()
					} else {
						s.isVerified = true;
						t.introIllegalTip.hide();
						t.signatureTip.hide()
					}
				})
			}
		});
		e.add(n);
		e.add(a);
		e.addAjax(o);
		e.addAjax(l)
	};
	return o
});
define("common/Login/tpl", function(e, t, n) {
	var i = {
		login: '<form action="https://reg.163.com/logins.jsp" method="POST" id="minLoginForm" name="loginform" target="_self">                <div class="login-area">                   <h2>\u6d77\u91cf\u76f4\u64ad\uff0c\u7ebf\u4e0a\u5a31\u4e50</h2>                   <p class="login-error-tip js-errorTip" style="display: none;"><em class="png24 icon-error"></em><span></span></p>                   <p class="login-item">                      <input class="login-mail" type="text" name="username" id="poplayer_username" autocomplete="off" placeholder="\u7f51\u6613\u90ae\u7bb1\u5e10\u53f7">                      <label class="login-default-text js-defaultText">\u7f51\u6613\u90ae\u7bb1\u5e10\u53f7</label>                   </p>                   <p class="login-item">                      <input class="login-pwd" type="password" name="password" id="poplayer_passport" placeholder="\u5bc6\u7801">                      <label class="login-default-text js-defaultText">\u5bc6\u7801</label>                   </p>                   <p class="login-check"><label class="fl"><input type="checkbox" value="1" name="savelogin" checked="checked">\u4e0b\u6b21\u81ea\u52a8\u767b\u5f55</label><a href="http://reg.163.com/RecoverPasswd1.shtml">\u5fd8\u8bb0\u5bc6\u7801</a></p>                   <input type="hidden" name="url" value="<%=url%>"/>                   <input type="hidden" value="bobo" name="product"/>                   <input type="hidden" value="bobo.com,163.com" name="domains"/>                   <input type="hidden" name="type" value="1"/>                   <p><button class="login-btn" type="submit" href="javascript:;">\u767b\u5f55</button></p>                </div>              </form>              <div class="no-account">                 <p>\u8fd8\u6ca1\u6709\u8d26\u53f7\uff1f</p>                 <p><a class="regist-ntes no-account-login" href="http://reg.163.com/reg/reg.jsp?product=bobo&url=<%=url%>&loginurl=<%=url%>" data-keyfrom="navi.login163"><em class="png24 icon-ntes"></em>\u6ce8\u518c\u7f51\u6613\u90ae\u7bb1</a></p>                 <p>\u6216\u8005\u7528\u793e\u4ea4\u8d26\u53f7\u767b\u5f55</p>                 <p><a class="qq-login no-account-login" href="http://reg.163.com/outerLogin/oauth2/connect.do?target=1&domains=bobo.com,163.com&product=bobo&url=<%=url%>&url2=<%=url%>" data-keyfrom="navi.loginqq"><em class="png24 icon-qq"></em>\u4f7f\u7528\u817e\u8bafQQ\u767b\u5f55</a></p>                 <p><a class="sina-login no-account-login" href="http://reg.163.com/outerLogin/oauth2/connect.do?target=3&domains=bobo.com,163.com&product=bobo&url=<%=url%>&url2=<%=url%>" data-keyfrom="navi.loginsina"><em class="png24 icon-sina"></em>\u4f7f\u7528\u65b0\u6d6a\u5fae\u535a\u767b\u5f55</a></p>              </div>',
		improUserInfo: '<form action="/user/userEdit?action=edit" method="post" id="userEdit" target="_self">                     <div class="loginLayer-info">                        <h2>\u6b22\u8fce\u6765\u5230BoBo!\u5b8c\u5584\u4e2a\u4eba\u4fe1\u606f\uff0c\u5373\u53ef\u5f00\u59cb\u7cbe\u5f69bo\u751f\u6d3b</h2>                        <p><label for="nickName"><em></em>\u6635\u79f0\uff1a</label>                        <input name="nick" class="login-nickname" id="nickName" type="text" value="<%=nick%>">                        <input type="hidden"  name="avatar" value="http://img2.cache.netease.com/bobo/image/avatar150.png"/>                        <span class="nickname-error-tip" id="nicknameTip" style="display:none;"><em class="png24 icon-error"></em>\u6635\u79f0\u957f\u5ea6\u8981\u57282-10\u4e2a\u5b57\u4e4b\u95f4</span>                        <span class="nickname-error-tip" id="nicknameErrorTip" style="display:none;"><em class="png24 icon-error"></em>\u7528\u6237\u540d\u91cd\u590d</span>                        <span class="nickname-error-tip" id="nicknameIllegalTip" style="display:none;"><em class="png24 icon-error"></em>\u6d89\u53ca\u654f\u611f\u8bcd\u8bed\uff0c\u8bf7\u91cd\u65b0\u7f16\u8f91</span>                        </p>                        <p>                           <label><em></em>\u6027\u522b\uff1a</label><span class="user-sex"><input type="radio" class="js-gender" name="sex" checked="checked" value="1"> \u7537</span><span class="user-sex"><input class="js-gender" name="sex" type="radio" value="2">\u5973</span>                        </p>                        <p>                        <label><em></em>\u751f\u65e5\uff1a</label>                        <select class="login-select-year" name="userYear" id="year">                        </select>                        <input type="hidden" value="2014" id="yearhd"/>                        <select class="login-select-date" name="userMonth" id="month">                        </select>                        <input type="hidden" value="01" id="monthhd"/>                        <select class="login-select-date" name="userDay" id="day">                        </select>                        <input type="hidden" value="01" id="dayhd"/>                     </p>                     <p>                        <label><em></em>\u6240\u5728\u5730\uff1a</label>                        <select class="login-select-place" name="pid" id="province">                        </select>                        <input type="hidden" value="1" id="provincehd"/>                        <select class="login-select-place" name="cid" id="city">                        </select>                        <input type="hidden" value="1" id="cityhd"/>                        <select class="login-select-city" name="aid" id="county">                        </select>                        <input type="hidden" value="1" id="countyhd"/>                     </p>                     <input type="hidden" name="url" value="<%=url%>"/>                     <p><input type="submit" class="orange-btn loginLayer-info-confirm" href="javascript:;" value="\u786e\u5b9a"/></p>                     </div>                     </form>'
	};
	return i
});
define("common/NaviTip", function(e, t, n) {
	var i = e("basic/jquery");
	var r = e("basic/oop/Class");
	var s = r.extend({
		init: function(e, t) {
			this.$elem = null;
			this.data = e;
			this.config = t;
			this._timer = null
		},
		render: function() {
			this.$elem = i('<p class="tips" style="z-index:1600"><span class="correct js-top-tip"></span></p>').appendTo("body");
			if (i.browser.msie && parseInt(i.browser.version, 10) < 7) {
				i(window).bind("scroll resize", i.proxy(this.onSetTopPosition, this))
			}
			this.$topTip = this.$elem.find(".js-top-tip");
			return this.$elem
		},
		show: function(e, t) {
			if (this.$elem === null) {
				this.render()
			}
			if (!e || e === "") {
				this.$elem.hide();
				return
			}
			this._timer && clearTimeout(this._timer);
			this.$topTip.removeClass("error");
			t = t || "success";
			this.$topTip.html(e);
			if (t === "error") {
				this.$topTip.addClass("error")
			}
			this.onSetPosition();
			this.$elem.hide().fadeIn(700);
			this.onClose()
		},
		onClose: function() {
			var e = this;
			this._timer = setTimeout(function() {
				e.$elem.fadeOut(1e3)
			}, 2e3)
		},
		onSetPosition: function() {
			this.$elem.css("marginLeft", "-" + this.$elem.innerWidth() / 2 + "px")
		},
		onSetTopPosition: function() {
			var e = i(window).scrollTop() || 0;
			this.$elem.css("top", e + "px")
		}
	});
	var a = null;
	return {
		getInstance: function() {
			if (a == null) {
				a = new s
			}
			return a
		},
		show: function(e, t) {
			if (i.isPlainObject(arguments[0])) {
				this.getInstance().show(arguments[0].text, arguments[0].type)
			} else {
				this.getInstance().show(e, t)
			}
		}
	}
});
define("common/Reg/Reg", function(e, t, n) {
	var i = e("basic/jquery");
	var r = e("common/Log/Logger");
	i(document).delegate(".js-need-reg", "click", i.proxy(function(e) {
		if (!CONFIG["islogined"]) {
			i(document).trigger("error", ["needReg"]);
			e.preventDefault();
			r.triggerLog(i(e.currentTarget))
		}
	}, this))
});
define("common/Reg/RegBox", function(e, t, n) {
	var i = e("basic/jquery");
	var r = e("basic/oop/Class");
	var s = e("common/FloatWin/FloatWin");
	var a = e("basic/Util");
	var o = e("common/Reg/tpl");
	var l = r.extend({
		preRender: function() {
			var e = {
				url: a.encodeSpecialHtmlChar(window.location.href)
			};
			return {
				content: a.formatTemplate(o.reg, e)
			}
		},
		postRender: function(e) {
			e.addClass("loginLayer");
			this.$elem = e;
			this.$elem.find(".dialogLayer-hd").find(".js-close").removeClass("btn-close").addClass("btn-login-close");
			this.$elem.find(".dialogLayer-hd").append('<a class="receive-now" href="http://www.bobo.com/special/pcjf/" target="_blank"></a>')
		},
		show: function() {
			s.PopWin({
				preRender: i.proxy(this.preRender, this),
				postRender: i.proxy(this.postRender, this)
			}).show()
		}
	});
	return l
});
define("common/Reg/tpl", function(e, t, n) {
	var i = {
		reg: '<div class="login-area">            <h2>\u6d77\u91cf\u76f4\u64ad\uff0c\u7ebf\u4e0a\u5a31\u4e50</h2>            <p><a class="ntes-regist" href="http://reg.163.com/reg/reg.jsp?product=bobo&url=<%=url%>&loginurl=<%=url%>" data-keyfrom="navi.reg163">\u6ce8\u518c\u7f51\u6613\u90ae\u7bb1</a></p>            <p>\u5df2\u6709\u5e10\u53f7\uff0c<a class="cBlue js-need-login" href="javascript:;">\u767b\u5f55</a></p>         </div>         <div class="no-account">         <p>\u6216\u8005\u7528\u793e\u4ea4\u5e10\u53f7\u767b\u5f55</p>         <p><a class="qq-login no-account-login" href="http://reg.163.com/outerLogin/oauth2/connect.do?target=1&domains=bobo.com,163.com&product=bobo&url=<%=url%>&url2=<%=url%>" data-keyfrom="navi.regqq"><em class="png24 icon-qq"></em>\u4f7f\u7528\u817e\u8bafQQ\u767b\u5f55</a></p>         <p><a class="sina-login no-account-login" href="http://reg.163.com/outerLogin/oauth2/connect.do?target=3&domains=bobo.com,163.com&product=bobo&url=<%=url%>&url2=<%=url%>" data-keyfrom="navi.regsina"><em class="png24 icon-sina"></em>\u4f7f\u7528\u65b0\u6d6a\u5fae\u535a\u767b\u5f55</a></p>         </div>'
	};
	return i
});
define("common/RollSlider", function(e, t, n) {
	var i = e("basic/jquery");
	var r = e("basic/oop/Class");
	var s = r.extend({
		$elem: null,
		$current: null,
		current: 0,
		timer: null,
		slideWidth: 0,
		slideCount: 0,
		reelWidth: 0,
		pageArr: [],
		$anim: null,
		DEFAULT: {
			viewNum: 1,
			autoPlay: false,
			interval: 2e3,
			pauseOnHover: true,
			reDelegate: false,
			duration: 500,
			rollCycle: false,
			type: "roll",
			tabClass: "current",
			window: "",
			reel: "",
			slideItem: "",
			pageItem: "",
			prevBtn: "",
			nextBtn: "",
			rollCallBack: null
		},
		init: function(e, t) {
			this.$elem = i(e);
			this.config = i.extend({}, this.DEFAULT, t);
			this.initRel();
			this.addAttrRel();
			this.rollSwitch();
			this.bindEvent()
		},
		initRel: function() {
			if (this.config.rollCycle) {
				var e = this.$elem.find(this.config.slideItem);
				var t = e.clone();
				this.$elem.find(this.config.reel).append(t);
				this.current = this.$elem.find(this.config.slideItem).size() / 2
			}
			this.slideWidth = this.$elem.find(this.config.slideItem).outerWidth();
			this.slideCount = this.$elem.find(this.config.slideItem).size();
			this.reelWidth = this.slideWidth * this.slideCount;
			this.$elem.find(this.config.reel).css({
				width: this.reelWidth,
				position: "absolute",
				left: -this.current * this.slideWidth
			})
		},
		addAttrRel: function() {
			this.pageArr = this.$elem.find(this.config.pageItem);
			for (var e = 0; e < this.pageArr.length; e++) {
				i(this.pageArr[e]).attr("rel", e + 1)
			}
		},
		bindEvent: function() {
			!this.config.reDelegate && this.$elem.undelegate();
			this.config.pageItem && this.$elem.delegate(this.config.pageItem, "click", i.proxy(this.onPageClick, this));
			this.config.prevBtn && this.$elem.delegate(this.config.prevBtn, "click", i.proxy(this.onPagePrev, this));
			this.config.nextBtn && this.$elem.delegate(this.config.nextBtn, "click", i.proxy(this.onPageNext, this));
			if (this.config.pauseOnHover) {
				this.$elem.hover(i.proxy(this.onEnter, this), i.proxy(this.onLeave, this))
			}
		},
		onEnter: function(e) {
			this.timer && clearInterval(this.timer)
		},
		onLeave: function(e) {
			if (e.currentTarget !== e.target && this.$elem.has(e.target).length === 0) return;
			this.rollSwitch()
		},
		onPagePrev: function(e) {
			if (this.$anim != null) return;
			this.current -= 1;
			if (!this.config.rollCycle && this.current < 0) {
				this.current = 0
			} else {
				this.roll();
				this.rollSwitch()
			}
		},
		onPageNext: function(e) {
			if (this.$anim != null) return;
			this.current += 1;
			if (!this.config.rollCycle && this.current > this.slideCount - 1) {
				this.current = this.slideCount - 1
			} else {
				this.roll();
				this.rollSwitch()
			}
		},
		isEdge: function() {
			if (!this.config.rollCycle) return;
			if (this.current == this.slideCount - this.config.viewNum) {
				this.current = this.slideCount / 2 - 1;
				this.$elem.find(this.config.reel).css("left", -this.current * this.slideWidth)
			}
			if (this.current == 0) {
				this.current = this.slideCount / 2;
				this.$elem.find(this.config.reel).css("left", -this.current * this.slideWidth)
			}
		},
		addTabClass: function(e) {
			if (this.config.rollCycle) {
				e = e % (this.slideCount / 2);
				if (this.current == this.slideCount - 1) {
					e = this.slideCount / 2 - 1
				}
				if (this.current == 0) {
					e = 0
				}
			}
			this.$elem.find(this.config.pageItem).removeClass(this.config.tabClass);
			i(this.pageArr[e]).addClass(this.config.tabClass)
		},
		roll: function() {
			var e = this.current;
			var t = this;
			var n = e * this.slideWidth;
			this.addTabClass(e);
			t.$anim && t.$anim.clearQueue();
			t.$anim = this.$elem.find(this.config.reel).animate({
				left: -n
			}, t.config.duration, function() {
				t.$anim = null;
				t.isEdge();
				t.config.rollCallBack && t.config.rollCallBack(t.current)
			})
		},
		onPageClick: function(e) {
			if (this.$anim != null) return;
			var t = i(e.target).attr("rel") - 1;
			this.current = parseInt(t, 10);
			this.roll();
			this.rollSwitch();
			return false
		},
		rollSwitch: function() {
			var e = this;
			if (!this.config.autoplay) {
				return
			}
			e.timer && clearInterval(e.timer);
			e.timer = setInterval(function() {
				e.onPageNext()
			}, e.config.interval)
		}
	});
	return s
});
define("common/SuggestList", function(e, t, n) {
	var i = e("basic/jquery");
	var r = e("basic/oop/Class");
	var s = r.extend({
		cacheData: null,
		binded: null,
		config: null,
		$elem: null,
		hideTimer: null,
		init: function(e, t) {
			this.$elem = i(e);
			this.config = t;
			this.$elem.delegate("li", "hover", i.proxy(this.onHover, this)).delegate("li", "click", i.proxy(this.onClick, this));
			if (this.config && !this.config.disableScroll) {
				this.$elem.bind("scroll", i.proxy(this.onScroll, this))
			}
		},
		onHover: function(e) {
			var t = i(e.target);
			if (e.target.tagName.toUpperCase() !== "LI") {
				t = t.parents("li").first()
			}
			if (!t.hasClass("disable")) {
				t.addClass("current").siblings(".current").removeClass("current")
			}
		},
		onCurrentFirst: function() {
			this.$elem.children("[className!=disable]").first().addClass("current")
		},
		onScroll: function(e) {
			clearTimeout(this.hideTimer);
			if (!this.bindBodyClick) {
				i(document.body).one("click", i.proxy(function() {
					this.bindBodyClick = false;
					this.hide()
				}, this));
				this.bindBodyClick = true
			}
		},
		show: function(e) {
			clearTimeout(this.hideTimer);
			if (e === this.cacheData) {
				return false
			}
			this.cacheData = e;
			return true
		},
		hide: function() {
			clearTimeout(this.hideTimer);
			this.hideTimer = setTimeout(i.proxy(function() {
				this.unbindEvent();
				this.$elem.detach()
			}, this), 200)
		},
		bindEvent: function() {
			if (!this.binded) {
				i(document.body).bind("keydown", i.proxy(this.onKey, this));
				this.binded = true
			}
		},
		unbindEvent: function() {
			if (this.binded) {
				i(document.body).unbind("keydown", i.proxy(this.onKey, this));
				this.binded = false
			}
		},
		onKey: function(e) {
			var t = this.$elem.find("li");
			if (e.which === 38 || e.which === 40) {
				var n = t.filter(".current").removeClass("current");
				var i = null;
				if (e.which === 38) {
					i = this.getPrevElem(n, t)
				} else {
					i = this.getNextElem(n, t)
				} if (i) {
					i.addClass("current")
				}
				e.preventDefault()
			}
			if ((e.which === 13 || e.which === 9) && t.size() > 0) {
				this.onEnter(e);
				e.preventDefault()
			}
		},
		getPrevElem: function(e, t) {
			var n = e.prev("li");
			n = n.size() > 0 ? n : t.last();
			if (n.hasClass("disable")) {
				return this.getPrevElem(n, t)
			} else {
				return n
			}
		},
		getNextElem: function(e, t) {
			var n = e.next("li");
			n = n.size() > 0 ? n : t.first();
			if (n.hasClass("disable")) {
				return this.getNextElem(n, t)
			}
			return n
		},
		onEnter: function(e) {
			throw new Error("Not Implemented")
		},
		onClick: function(e) {
			var t = i(e.target);
			if (e.target.tagName.toUpperCase() !== "LI") {
				t = t.parents("li").first()
			}
			if (!t.hasClass("disable")) {
				var n = t.attr("n");
				this.$elem.trigger("select", [n])
			}
		}
	});
	return s
});
define("common/Tab", function(e, t, n, i) {
	var r = e("basic/jquery");
	var s = function(e) {
		this.options = {
			wraper: r(document.body),
			className: "cur",
			eventType: "click",
			tabClass: ".js-tab",
			func: function() {},
			tabIndex: 1
		};
		for (var t in e) {
			this.options[t] = e[t]
		}
		this.current = {};
		var n = this;
		n.options.wraper.find(this.options["tabClass"]).each(function(e, t) {
			if (e + 1 == n.options.tabIndex) {
				r(t).addClass(n.options.className);
				n.current = t
			}(function(e, t) {
				r(t)[n.options.eventType](function() {
					n.options.tabIndex = e + 1;
					n.trigger(t, e);
					return false
				})
			})(e, t)
		})
	};
	s.prototype.trigger = function(e, t) {
		var n = this;
		r(n.current).removeClass(n.options.className);
		r(e).addClass(n.options.className);
		n.current = e;
		n.options.func.apply(null, [e, t])
	};
	return s
});
define("common/ToTop", function(e, t, n) {
	var i = e("basic/jquery");
	var r = i("#toTop").bind("click", function() {
		i(window).scrollTop(0)
	});
	var s = null;
	var a = false;
	i(document).ready(function() {
		setTimeout(function() {
			i(window).scrollTop(0)
		}, 100)
	});
	i(".js-showEwm").bind("click", function() {
		i("#Ewm2").toggle()
	});
	i(".js-showTel").on({
		mouseover: function() {
			i("#Tel").show()
		},
		mouseout: function() {
			i("#Tel").hide()
		},
		click: function() {
			i("#Tel").toggle()
		}
	});

	function o() {
		var e = i(window).scrollTop();
		if (e > 0) {
			if (a) {
				r.removeClass("hidden");
				a = false
			}
		} else {
			if (!a) {
				r.addClass("hidden");
				a = true
			}
		}
	}
	o();
	i(window).bind("scroll resize", function(e) {
		clearTimeout(s);
		s = setTimeout(o, 100)
	})
});
define("common/Validate", function(e, t, n) {
	var i = e("basic/jquery");

	function r(e, t) {
		if (!i(e)) {
			return
		}
		this.commonCollection = [];
		this.ajaxCollection = [];
		this.func = t || function() {};
		var n = this;
		i(e).submit(function() {
			if (n.commitCheck() == true) {
				return n.func()
			} else {
				return false
			}
		})
	}
	r.prototype.add = function(e) {
		this.commonCollection.push(e)
	};
	r.prototype.addAjax = function(e) {
		this.ajaxCollection.push(e)
	};
	r.prototype.commitCheck = function() {
		var e = [];
		for (var t = 0; t < this.commonCollection.length; t++) {
			if (!this.commonCollection[t].check()) {
				e.push(this.commonCollection[t]);
				break
			}
		}
		for (var t = 0; t < this.ajaxCollection.length; t++) {
			if (!this.ajaxCollection[t].isVerified) {
				e.push(this.ajaxCollection[t]);
				break
			}
		}
		return e.length == 0
	};

	function s(e) {
		var t = this;
		t.options = {
			node: document.body,
			clear: function() {},
			isAutoCheck: false,
			isAutoClear: false,
			check: function() {},
			isVerified: false
		};
		for (var n in e) {
			t.options[n] = e[n]
		}
		t.isVerified = t.options.isVerified;
		t.node = t.options.node;
		t.check = t.options.check;
		t.options.isAutoCheck == true && t.options.node.blur(function() {
			t.options.check.apply(t)
		});
		t.options.isAutoClear == true && t.options.node.focus(function() {
			t.options.clear.apply(t)
		})
	}
	return {
		Validate: r,
		ValidateItem: s
	}
});
define("common/Win", function(e, t, n, i) {
	var r = e("basic/jquery");
	var s = document.documentElement,
		a = document.body;
	var o = Array.prototype.slice;
	Function.prototype.bind = function() {
		if (!arguments.length) {
			return this
		}
		var e = this,
			t = o.call(arguments),
			n = t.shift();
		return function() {
			return e.apply(n, t.concat(o.call(arguments)))
		}
	};
	var l = new function() {
		var e = document.createElement("div"),
			t = false;
		e.id = "shade";
		a.appendChild(e);
		if (r.browser.msie && parseInt(r.browser.version, 10) < 7) {
			e.innerHTML = "<iframe style='position:absolute;width:100%;height:100%;_filter:alpha(opacity=0);opacity=0;border:1px solid #DDD;z-index:-1;top:100;left:0;'></iframe>"
		}
		this.show = function() {
			var n = this;
			n._setposEvent = null;
			if (!t) {
				n.shadeResize(e);
				if (r.browser.msie && parseInt(r.browser.version, 10) < 7) {
					var i = function() {
						n.IE6Position()
					};
					n.IE6Position();
					n._setposEvent && window.detachEvent("scroll", i);
					n._setposEvent = window.attachEvent("scroll", i)
				}
				e.style.display = "block";
				t = true
			}
			n.shadeSizeFunc = function() {
				n.shadeResize(e)
			};
			r(window).bind("resize", n.shadeSizeFunc)
		};
		this.shadeResize = function(e) {
			e.style.width = Math.max(s.scrollWidth, s.clientWidth) + "px";
			e.style.height = Math.max(s.scrollHeight, s.clientHeight) + "px";
			e.style.top = "0px"
		}, this.IE6Position = function() {
			var t = Math.max(s.scrollHeight, s.clientHeight);
			e.style.width = Math.max(s.scrollWidth, s.clientWidth) + "px";
			e.style.position = "absolute";
			e.style.height = Math.min(t, 3e3) + "px";
			e.style.top = Math.min(Math.max(t - 3e3, 0), Math.max(s.scrollTop - 3e3 + window.screen.availHeight, 0)) + "px"
		};
		this.hide = function() {
			var n = this;
			e.style.display = "none";
			t = false;
			r(window).unbind("resize", n.shadeSizeFunc)
		}
	};
	var c = function(e, t) {
		this.constructor = arguments.callee;
		var n = e.css("position").toLowerCase() === "fixed",
			i = {
				x: e.offset().left || 0 - (n ? document.body.scrollLeft : 0),
				y: e.offset().top || 0 - (n ? document.body.scrollTop : 0)
			},
			o, l, c, u;

		function f(t) {
			var r = {
				x: Math.max(0, Math.min(s.scrollWidth - e.width(), t.pageX - o.x)),
				y: Math.max(0, Math.min(s.scrollHeight - e.height(), t.pageY - o.y))
			};
			e.css("left", r.x + "px");
			e.css("top", r.y + "px");
			i = {
				x: r.x - (n ? document.body.scrollLeft : 0),
				y: r.y - (n ? document.body.scrollTop : 0)
			};
			c && c(e, this, r);
			t.cancelBubble = true
		}

		function d() {
			if (e.releaseCapture) {
				e.releaseCapture()
			} else if (window.captureEvents) {
				window.captureEvents(Event.MOUSEMOVE | Event.MOUSEUP)
			}
			r(a).unbind("mousemove", f);
			r(a).unbind("mouseup", d);
			u && u(e)
		}

		function p(t) {
			if (e.setCapture) {
				e.setCapture()
			} else if (window.captureEvents) {
				window.captureEvents(Event.MOUSEMOVE | Event.MOUSEUP)
			}
			o = {
				x: t.pageX - e.offset().left,
				y: t.pageY - e.offset().top
			};
			r(a).mousemove(f);
			r(a).mouseup(d);
			l && l(e, this);
			t.preventDefault()
		}
		this.setOnDragStart = function(e) {
			l = e
		};
		this.setOnMove = function(e) {
			onMove = e
		};
		this.setOnStopMove = function(e) {
			onStopMove = e
		};
		this.init = function() {
			t.mousedown(function(e) {
				p(e)
			})
		}
	};
	var u = function(e, t, n, i, s) {
		if (!arguments.length) {
			return
		}
		var a = this;
		a.constructor = arguments.callee;
		a._wrapper = e;
		a._showType = n || "display";
		a._showTypeObject = {
			display: {
				show: "block",
				hidden: "none"
			},
			visibility: {
				show: "visible",
				hidden: "hidden"
			}
		};
		a._isShade = t || false;
		a._isDeleteNode = i || false;
		a._isShowed = e.css(a._showType).toLowerCase() != a._showTypeObject[a._showType].hidden;
		a._isFixed = true;
		e.css("position", "fixed");
		if (r.browser.msie && parseInt(r.browser.version, 10) < 7) {
			a._isFixed = false;
			e.css("position", "absolute")
		}
		a.isClickClose = s;
		new c(e, e.find(".js-move")).init();
		e.delegate(".js-close", "click", function(e) {
			a.hide();
			return false
		}).bind("drag", function(e) {
			e.cancelBubble = true
		})
	};
	u.prototype = {
		getWidth: function() {
			return this._wrapper.width()
		},
		getHeight: function() {
			return this._wrapper.height()
		},
		show: function(e, t, n) {
			var i = this;
			if (!i._isShowed) {
				var a = i._wrapper;
				if (r.browser.msie && parseInt(r.browser.version, 10) < 7) {
					i._tempTop = s.scrollTop;
					i._tempScroll = i._scroll
				}
				a.css(i._showType, i._showTypeObject[i._showType].show);
				i.resize(e, t, a);
				i.onShow && i.onShow();
				i._isShowed = true;
				n && (i._hideTimer = setTimeout(function() {
					i.hide()
				}, n * 1e3));
				r(document).keydown(i.keydownListionEvent.bind(i));
				if (i.isClickClose) {
					r(document).click(i.clickListionEvent.bind(i))
				}
				i.sizeFunc = function() {
					i.resize(e, t, a)
				};
				r(window).bind("resize", i.sizeFunc)
			}
		},
		resize: function(e, t, n) {
			var i = this;
			var r, o;
			if (i._isFixed) {
				r = null == e || isNaN(e) ? parseInt((s.clientWidth - n.width()) / 2) : e - s.scrollLeft;
				o = null == t || isNaN(t) ? parseInt((s.clientHeight - n.height()) / 2.3) : t - (s.scrollTop == 0 ? a.scrollTop : s.scrollTop)
			} else {
				var c = self.pageYOffset || document.documentElement && document.documentElement.scrollTop || document.body.scrollTop;
				r = null == e || isNaN(e) ? parseInt((s.clientWidth - n.width()) / 2 + s.scrollLeft) : e;
				o = null == t || isNaN(t) ? parseInt((s.clientHeight - n.height()) / 2.3 + c) : t
			}
			n.css("left", r + "px");
			n.css("top", o + "px");
			i._isShade && l.show()
		},
		hide: function() {
			var e = this;
			if (e._isShowed) {
				e.onHide && e.onHide();
				e._isShowed = false;
				if (e._hideTimer) {
					clearTimeout(e._hideTimer);
					e._hideTimer = null
				}
				e._isShade && l.hide();
				r(document).unbind("keydown", e.keydownListionEvent.bind(e));
				if (e.isClickClose) {
					r(document).unbind("click", e.clickListionEvent.bind(e))
				}
				if (e._isDeleteNode) {
					e._wrapper.detach()
				} else {
					var t = e._showTypeObject[e._showType].hidden;
					e._wrapper.css(e._showType, t);
					e._wrapper.css({
						left: "0",
						top: "0"
					})
				}
				r(window).unbind("resize", e.sizeFunc)
			}
		},
		_scroll: function() {
			var e = this;
			e._wrapper.css("top", parseInt(e._wrapper.style.top) + s.scrollTop - e._tempTop + "px");
			e._tempTop = s.scrollTop
		},
		keydownListionEvent: function(e) {
			var t = this;
			if (e.keyCode == 27) {
				t.hide()
			}
		},
		clickListionEvent: function(e) {
			var t = this,
				n = e.target ? e.target : event.srcElement;
			do {
				if (n.className == "dialog") return;
				if (n.tagName.toUpperCase() == "BODY" || n.tagName.toUpperCase() == "HTML") {
					t.hide();
					return
				}
				n = n.parentNode
			} while (n.parentNode)
		}
	};
	return u
});
define("common/buy", function(e, t, n) {
	"use strict";
	var i = e("basic/jquery");
	var r = e("shop/BuyBox");
	var s = e("shop/BuySpecialRoomBox");
	var a = e("common/NaviTip");
	var o = e("common/FloatWin/FloatWin");
	var l = e("common/Log/Logger");
	var c = null;
	var u = function(e) {
		if (!CONFIG["islogined"]) {
			i(document).trigger("error", ["needLogin"]);
			return
		}
		e.preventDefault();
		if (i.isPlainObject(c) && c.readyState !== 4) {
			return
		}
		var t = i(e.currentTarget);
		var n = t.attr("data-method") || "buy";
		var o = i.parseJSON(t.attr("data-config") || "{}");
		i.extend(o, {
			payMethod: n
		});
		var u = t.attr("data-itemId");
		if (o.sepcialroom && o.sepcialroomRenew) {
			c = i.ajax({
				type: "GET",
				url: "/shop/houseRenew",
				data: {
					itemId: u,
					method: n
				},
				dataType: "json",
				success: function(e) {
					if (e.status == 1) {
						new s(e, o).show();
						l.triggerLog(t, {
							method: n + "ing"
						})
					} else {
						f(e)
					}
				},
				error: function() {
					a.show({
						type: "error",
						text: "\u7f51\u7edc\u8bf7\u6c42\u51fa\u9519\u5566\uff0c\u8bf7\u5237\u65b0\u91cd\u8bd5\uff01"
					})
				}
			})
		} else {
			c = i.ajax({
				type: "GET",
				url: "/shop/item",
				data: {
					itemId: u,
					method: n
				},
				dataType: "json",
				success: function(e) {
					if (e.status == 1) {
						if (e.item.shopType == 3) {
							e.item.priceMonth = e.item.paramMap.newPriceMonth;
							e.item.priceYear = e.item.paramMap.newPriceYear
						}
						if (e.item.shopType == 5) {
							e.item.priceMonth = e.item.specialPriceMonth;
							e.item.priceYear = e.item.specialPriceYear
						}
						if (o.sepcialroom) {
							new s(e, o).show()
						} else {
							new r(e, o).show()
						}
						l.triggerLog(t, {
							method: n + "ing"
						})
					} else {
						f(e)
					}
				},
				error: function() {
					a.show({
						type: "error",
						text: "\u7f51\u7edc\u8bf7\u6c42\u51fa\u9519\u5566\uff0c\u8bf7\u5237\u65b0\u91cd\u8bd5\uff01"
					})
				}
			})
		}
	};

	function f(e) {
		if (e.status == 509) {
			i(document).trigger("error", ["needLogin"])
		} else if (e.status == 603) {
			new o.ConfirmsWin({
				content: "\u60a8\u8fd8\u6ca1\u52a0\u5165\u5bb6\u65cf\uff0c\u4e0d\u80fd\u8d2d\u4e70\u9ad8\u7ea7\u5bb6\u65cf\u5fbd\u7ae0\u54e6\uff01<br/>\u5feb\u53bb\u52a0\u5165\u4e00\u4e2a\u5bb6\u65cf\u5427\uff01",
				confirmFunc: function() {
					location.href = "/family"
				}
			}).show()
		} else {
			new o.AlertWin({
				content: "\u5f88\u62b1\u6b49\uff0c\u51fa\u9519\u5566\uff01"
			}).show()
		}
	}
	i(document).delegate(".js-goBuy", "click", function(e) {
		u(e)
	})
});
define("common/error/ErrorCatch", function(e, t, n) {
	function i(e) {}
	i.prototype.catches = function(e, t, n) {
		var i = this.getErrorParmas(e, t, n);
		this.send(i)
	};
	i.prototype.send = function(e) {
		var t = function() {
			var t = [];
			for (var n in e) {
				t.push(encodeURIComponent(n) + "=" + e[n])
			}
			return t.join("&")
		}();
		this.sendTrackLog(t)
	};
	i.prototype.getErrorParmas = function(e, t, n) {
		return {
			pageid: window._ba_utm_s || 0,
			file: encodeURIComponent(t) || "",
			url: encodeURIComponent(location.href),
			message: encodeURIComponent(e) || "",
			line: encodeURIComponent(n) || 0,
			samedomain: top == window
		}
	};
	i.prototype.sendRequest = function(e) {
		if (!e) return;
		var t = document.createElement("img");
		t.onload = function() {
			t.onload = null;
			t = null
		};
		t.src = e
	};
	i.prototype.sendTrackLog = function(e) {
		var e = "/perform.do?key=jserror&_t=" + (new Date).getTime() + "&" + e;
		this.sendRequest(e)
	};
	i.prototype.start = function() {
		var e = this;

		function t(t, n, i) {
			e.catches(t, n, i)
		}
		window.onerror = t
	};
	return i
});
define("common/error/ErrorManager", function(e, t, n) {
	var i = e("basic/jquery");
	var r = e("common/Login/LoginBox");
	var s = e("common/Reg/RegBox");
	var a = e("common/error/ErrorCatch");
	var o = {
		networkErrorCount: 0,
		loginBox: null,
		regBox: null,
		bindEvent: function() {
			i(document).bind("error", i.proxy(this.handleError, this))
		},
		handleError: function(e, t, n) {
			var a = null;
			if (t === "needLogin") {
				if (!this.loginBox) {
					this.loginBox = new r
				}
				this.loginBox.show()
			} else if (t === "needReg") {
				if (!this.regBox) {
					this.regBox = new s
				}
				this.regBox.show()
			} else if (i.isPlainObject(n)) {
				if (t === "network" && this.networkErrorCount < 2) {
					this.networkErrorCount++;
					return
				}
			}
		}
	};
	return o
});
define("common/nav/Edit", function(e, t, n) {
	var i = e("basic/jquery");
	var r = e("basic/Util");
	var s = e("common/Validate");
	var a = e("common/NaviTip");

	function o(e) {
		this.nickNameNav = i("#nickNameNav");
		this.repeatErrorTips = i("#repeatErrorTips");
		this.errorTips = i("#errorTips");
		this.loginEdit = i("#loginEdit");
		this.userNickName = i("#userNickName");
		this.loginEditSubmit = i("#loginEditSubmit");
		this.nameEditForm = i("#nameEditForm");
		this.loginEditCancel = i("#loginEditCancel");
		this.illegalErrorTip = i("#illegalErrorTip");
		var t = this;
		t.nickNameNav.val(e);
		var n = new s.Validate("#nameEditForm", function() {
			var e = t.nickNameNav.val();
			i.post("/user/checkNick.do", {
				nick: e
			}, function(n) {
				if (n.status == 1) {
					t.repeatErrorTips.hide();
					t.illegalErrorTip.hide();
					t.errorTips.hide();
					i.post("/user/updateUserNick", {
						nick: e
					}, function(n) {
						if (n.status == 1) {
							t.hide();
							t.callback(e)
						} else {
							a.show(n.errorMsg, "error")
						}
					})
				} else if (n.status == 2) {
					t.illegalErrorTip.show();
					t.repeatErrorTips.hide();
					t.errorTips.hide()
				} else if (n.status == 0) {
					t.repeatErrorTips.show();
					t.errorTips.hide();
					t.illegalErrorTip.hide()
				}
			});
			return false
		});
		var o = new s.ValidateItem({
			node: t.nickNameNav,
			check: function() {
				var e = i.trim(this.node.val());
				var n = r.getLength(e),
					s = r.getleftLength(e);
				if (n > 10 || s < 2) {
					t.errorTips.show();
					t.repeatErrorTips.hide();
					return false
				} else {
					t.errorTips.hide();
					return true
				}
			}
		});
		n.add(o);
		t.loginEditSubmit.click(function() {
			t.nameEditForm.submit()
		});
		t.loginEditCancel.click(function() {
			t.hide()
		});
		i("body").mousedown(function(e) {
			var n = e.target || e.srcElement;
			r.bubbleNodeNe(n, function(e) {
				if (e.id == "loginEdit") {
					return true
				}
				return false
			}, function() {
				t.hide()
			})
		})
	}
	o.prototype.hide = function() {
		this.loginEdit.hide()
	};
	o.prototype.callback = function(e) {};
	return o
});
define("common/nav/Nav", function(e, t, n) {
	var i = e("basic/jquery");
	var r = e("basic/Util");
	var s = e("common/FloatWin/FloatWin");
	var a = e("common/NaviTip");
	var o = e("common/nav/Public");
	var l = e("message/Reminder");
	var c = e("common/nav/Edit");
	var u = e("common/Log/Logger");

	function f() {
		if (CONFIG["islogined"] == true) {
			this.initUser();
			this.initMessage()
		}
		this.initSearch();
		this.initAchorBtn();
		this.initCoin()
	}
	f.prototype.initUser = function() {
		var e = this;
		this.logout = i("#logout");
		this.initUserPanel();
		this.initUserNameEdit();
		var t = encodeURI(r.delQueStr(window.location.href, "username"));
		e.logout.attr("href", "http://reg.163.com/Logout.jsp?url=" + t + "&product=bobo")
	};
	f.prototype.initSearch = function() {
		var e = this;
		this.searchInput = i("#searchInput");
		this.searchBtn = i("#searchBtn");
		this.searchTips = i("#searchTips");
		this.searchWraper = i("#searchWraper");
		this.loginInfoShow = i("#loginInfoShow");
		this.searchTips.click(function() {
			i(this).hide();
			e.searchInput.focus();
			e.searchWraper.addClass("search-active");
			e.searchBtn.addClass("btn-search2")
		});
		this.searchInput.click(function() {
			e.searchTips.hide();
			i(this).focus();
			e.searchWraper.addClass("search-active");
			e.searchBtn.addClass("btn-search2")
		});
		this.searchInput.blur(function() {
			var t = i(this).val();
			if (t == "") {
				e.searchTips.show()
			}
		});
		this.searchBtn.click(function() {
			var t = encodeURIComponent(e.searchInput.val());
			if (t != "") {
				window.location.href = "/search/nick/hot?searchWord=" + t
			}
			return false
		});
		this.searchWraper.hover(function() {
			i(this).addClass("search-hover");
			e.searchTips.html("\u6635\u79f0\u3001\u623f\u53f7\u3001\u5bb6\u65cf");
			e.searchTips.animate({
				width: 180
			}, "slow");
			e.searchInput.animate({
				width: 180
			}, "slow")
		}, function() {
			e.searchTips.html("");
			e.searchTips.animate({
				width: 75
			}, "slow");
			e.searchInput.animate({
				width: 75
			}, "slow");
			i(this).removeClass("search-hover");
			i(this).removeClass("search-active");
			e.searchBtn.removeClass("btn-search2");
			e.searchInput.blur()
		});
		this.searchInput.keydown(function(t) {
			if (t.keyCode == 13) {
				var n = encodeURIComponent(e.searchInput.val());
				if (n != "") {
					window.location.href = "/search/nick/hot?searchWord=" + n
				}
			}
		})
	};
	f.prototype.initAchorBtn = function() {
		i("body").delegate(".js-start-apply", "click", function() {
			var e = i(this).attr("href");
			i.get("/anchor/isAnchorOkLive.do", function(t) {
				if (t.status == 1) {
					window.location = e
				} else {
					a.show("\u60a8\u7684\u7533\u8bf7\u6b63\u5728\u5ba1\u6838,\u7a0d\u5b89\u52ff\u8e81\uff01", "error")
				}
			});
			return false
		})
	};
	f.prototype.initMessage = function() {
		new l
	};
	f.prototype.showEditPanel = function() {
		this.hideUserPanel();
		this.loginEdit.show()
	};
	f.prototype.showUserPanel = function() {
		this.loginUserCenter.show()
	};
	f.prototype.hideUserPanel = function() {
		this.loginUserCenter.hide()
	};
	f.prototype.initUserPanel = function() {
		var e = this;
		this.loginInfoShow = i("#loginInfoShow");
		this.loginUserCenter = i("#loginUserCenter");
		e.loginInfoShow.hover(function() {
			e.showUserPanel();
			u.triggerLog(i(this))
		}, function() {
			e.hideUserPanel()
		});
		this.loginUserCenter.mouseenter(function() {
			e.showUserPanel()
		}).mouseleave(function() {
			e.hideUserPanel()
		})
	};
	f.prototype.initUserNameEdit = function() {
		this.iconLogined = i("#iconLogined");
		this.userNickName = i("#userNickName");
		this.loginEdit = i("#loginEdit");
		var e = this;
		this.Edit = new c(e.userNickName.text());
		this.Edit.callback = function(t) {
			e.userNickName.text(t);
			i("#loginNickName").text(t)
		};
		e.iconLogined.click(function() {
			e.showEditPanel()
		})
	};
	f.prototype.initCoin = function() {
		var t = e("basic/Observer");
		t.subscribe({
			eventName: "buy:success",
			func: function(e) {
				e[0] && i("#loginCoin").text(e[0])
			}
		})
	};
	return new f
});
define("common/nav/Public", function(e, t, n) {
	var i = e("common/error/ErrorManager");
	i.bindEvent();
	e("common/Log/Log");
	e("common/Login/Login");
	e("common/Reg/Reg");
	e("common/play");
	var r = e("basic/jquery");
	var s = e("basic/cookie");
	var a = e("common/Login/ImproUserInfo");
	(new a).show();
	if (CONFIG["islogined"]) {
		r.get("/forceBindAccount", function(e) {
			if (e.status == 1) {
				var t = "isBindEpay" + CONFIG["userId"];
				if (!s(t)) {
					s(t, "1", {
						expires: 1
					});
					window.location.href = "/family/bindEpay"
				}
			}
		})
	}
	e("common/buy");
	var o = e("common/Feedback/Feedback")
});
define("common/play", function(e, t, n) {
	var i = e("basic/jquery");
	if (navigator.userAgent.match(/mobile/i)) {
		i(document).delegate(".js-play", "touchstart", function(e) {
			i(e.currentTarget).addClass("hover")
		});
		i(document).delegate(".js-play", "touchend", function(e) {
			i(e.currentTarget).removeClass("hover")
		})
	} else {
		var r = function(e, t) {
			var n = i(t.currentTarget);
			if (t.type === "mouseenter") {
				n.addClass(e);
				if (i.browser.msie && parseInt(i.browser.version, 10) < 7) {
					n.find(".icon-play").show()
				}
			} else {
				n.removeClass(e);
				if (i.browser.msie && parseInt(i.browser.version, 10) < 7) {
					n.find(".icon-play").hide()
				}
			}
		};
		i(document).delegate(".js-play", "hover", function(e) {
			r("hover", e)
		})
	}
});
define("home/AnchorType", function(e, t, n) {
	var i = e("basic/jquery");
	var r = e("basic/oop/Class");
	var s = e("basic/Util");
	var a = e("common/HoverSelect");
	var o = e("home/tpl");
	var l = r.extend({
		init: function() {
			this.$elem = i("#hotAnchor");
			this.$content = this.$elem.find(".js-content");
			this.$noContent = this.$elem.find(".js-noContent");
			this.hotLiveCount = 24;
			this.pageNum = 16;
			this.Data = {};
			this.getHotLive();
			this.bindEvent();
			this.inited = false
		},
		getHotLive: function() {
			this.bType = "/getHotlive";
			this.sType = "orderby=cost";
			this.eType = "";
			this.hType = "";
			this.getData()
		},
		bindEvent: function() {
			this.$elem.delegate(".js-bigTab a", "click", i.proxy(this.onBigTab, this)).delegate(".js-smallTab .js-tab", "click", i.proxy(this.onSmallTab, this)).delegate(".js-more", "click", i.proxy(this.onStatMore, this)).delegate(".js-levelSelect a", "click", i.proxy(this.onLevelSelect, this)).delegate(".js-areaSelect .js-tab a", "click", i.proxy(this.onAreaTab, this)).delegate(".js-areaSelect ul a, .js-areaSelect .js-myProvince", "click", i.proxy(this.areaSelect, this));
			i(window).bind("scroll.hotAnchorType resize.hotAnchorType", i.proxy(this.setTimeCheck, this));
			new a({
				$hoverSrc: this.$elem.find(".js-level"),
				$hoverTarget: this.$elem.find(".js-levelSelect")
			});
			new a({
				$hoverSrc: this.$elem.find(".js-area"),
				$hoverTarget: this.$elem.find(".js-areaSelect")
			})
		},
		getData: function() {
			this.hotLiveCount = 24;
			var e = this.bType.indexOf("?") == -1 ? "?" : "&";
			var t = this.bType + e + this.sType + (this.eType ? "&" + this.eType : "") + (this.hType ? "&" + this.hType : "");
			if (this.Data[t]) {
				this.render(this.Data[t]);
				return
			}
			var n = "/home" + t,
				r = this;
			i.ajax({
				url: n,
				dataType: "json",
				success: function(e) {
					r.render(e.anchors);
					r.Data[t] = e.anchors
				},
				error: function() {}
			})
		},
		render: function(e) {
			if (!e.length) {
				this.$noContent.show()
			} else {
				this.$noContent.hide()
			}
			this.$content.html(s.formatTemplate(o.hotLive, {
				Util: s,
				anchorData: e
			}));
			if (!this.inited) {
				this.inited = true;
				return
			}
			i("html,body").animate({
				scrollTop: this.$elem.offset().top - 5
			}, 500)
		},
		setTimeCheck: function() {
			var e = this;
			clearTimeout(e.timer);
			e.timer = setTimeout(i.proxy(e.checkScroll, e), 50)
		},
		checkScroll: function() {
			var e = i(window).scrollTop();
			var t = i(window).height();
			var n = this.$elem.find(".js-more").filter(":visible");
			if (n.length && e + t > n.parent().offset().top) {
				n.click()
			}
		},
		onBigTab: function(e) {
			e.preventDefault();
			var t = i(e.currentTarget);
			if (t.hasClass("cur")) return;
			this.$elem.find(".js-bigTab a").removeClass("cur");
			t.addClass("cur");
			this.$elem.find(".js-smallTab a").removeClass("cur").first().addClass("cur");
			this.bType = t.attr("data-bType");
			this.sType = this.$elem.find(".js-smallTab a").first().attr("data-sType");
			this.eType = "";
			this.hType = "";
			this.resetExtra();
			this.getData()
		},
		onSmallTab: function(e) {
			e.preventDefault();
			var t = i(e.currentTarget);
			var n = t.attr("data-sType");
			if (!n) return;
			if (t.hasClass("cur")) return;
			this.$elem.find(".js-smallTab .js-tab").removeClass("cur");
			t.addClass("cur");
			this.sType = n;
			this.getData()
		},
		onLevelSelect: function(e) {
			e.preventDefault();
			var t = i(e.currentTarget);
			var n = t.attr("data-sType");
			this.$elem.find(".js-level").addClass("cur").find("span").text(t.text());
			this.$elem.find(".js-levelSelect a").removeClass("cur");
			this.$elem.find(".js-levelSelect").hide();
			t.addClass("cur");
			if (n) {
				this.eType = n
			} else {
				this.eType = ""
			}
			this.getData()
		},
		onStatMore: function(e) {
			e.preventDefault();
			var t = i(e.currentTarget);
			var n = t.closest(".js-content").find("li");
			n.slice(this.hotLiveCount, this.hotLiveCount += this.pageNum).show().find("img").hide().fadeIn(700);
			var r = n.length;
			if (this.hotLiveCount >= r) {
				t.parent().hide()
			}
		},
		onAreaTab: function(e) {
			e.preventDefault();
			var t = i(e.currentTarget);
			this.$elem.find(".js-areaSelect .js-tab a").removeClass("cur");
			t.addClass("cur");
			var n = this.$elem.find(".js-areaSelect .js-tab a").index(t);
			this.$elem.find(".js-areaSelect ul").hide().eq(n).show()
		},
		areaSelect: function(e) {
			var t = i(e.currentTarget);
			var n = t.attr("data-sType");
			this.$elem.find(".js-area").addClass("cur").find("span").text(t.text());
			this.$elem.find(".js-areaSelect").hide();
			if (n) {
				this.hType = n
			} else {
				this.hType = ""
			}
			this.getData();
			return false
		},
		resetExtra: function() {
			this.$elem.find(".js-level").find("span").text("\u6240\u6709\u7b49\u7ea7");
			this.$elem.find(".js-levelSelect a").removeClass("cur");
			this.$elem.find(".js-area").find("span").text("\u6240\u6709\u5730\u533a");
			this.$elem.find(".js-areaSelect .js-tab a").first().click()
		}
	});
	new l
});
define("home/EditorReco", function(e, t, n) {
	var i = e("basic/jquery");
	var r = e("basic/oop/Class");
	var s = e("basic/Util");
	var a = e("home/tpl");
	var o = r.extend({
		init: function() {
			this.$elem = i("#editorReco");
			this.$homeSearch = this.$elem.find(".js-homeSearch");
			this.$searchInput = this.$elem.find(".js-searchInput");
			this.$searchBtn = this.$elem.find(".js-searchBtn");
			this.bindEvent()
		},
		bindEvent: function() {
			var e = this;
			this.$homeSearch.hover(function() {
				i(this).addClass("home-search2")
			}, function() {
				i(this).removeClass("home-search2")
			});
			this.$searchInput.bind("focus", function() {
				i(this).val("")
			});
			this.$searchInput.bind("blur", function() {
				if (i(this).val() == "") {
					i(this).val("\u6635\u79f0 / \u623f\u53f7 / \u5bb6\u65cf")
				}
			});
			this.$searchBtn.click(function() {
				var t = e.$searchInput.val();
				if (t != "" && t != "\u6635\u79f0 / \u623f\u53f7 / \u5bb6\u65cf") {
					window.location.href = "/search/nick/hot?searchWord=" + encodeURIComponent(t)
				}
				return false
			});
			this.$searchInput.keydown(function(t) {
				if (t.keyCode == 13) {
					var n = e.$searchInput.val();
					if (n != "" && n != "\u6635\u79f0 / \u623f\u53f7 / \u5bb6\u65cf") {
						window.location.href = "/search/nick/hot?searchWord=" + encodeURIComponent(n)
					}
				}
			})
		}
	});
	new o
});
define("home/Ewm", function(e, t, n) {
	var i = e("basic/jquery");
	var r = e("basic/oop/Class");
	var s = e("basic/Util");
	var a = e("common/Tab");
	var o = r.extend({
		init: function(e) {
			this.$elem = i(e);
			this.$content = this.$elem.find(".js-content");
			this.bindEvent()
		},
		bindEvent: function() {
			var e = this;
			this.$elem.delegate(".js-close", "click", function() {
				e.$elem.addClass("hidden")
			});
			this.$elem.delegate(".js-tab", "mouseenter", i.proxy(this.onTab, this))
		},
		onTab: function(e) {
			e.preventDefault();
			var t = i(e.currentTarget);
			t.addClass("btn-ewmtab2").siblings().removeClass("btn-ewmtab2");
			var n = t.attr("data");
			this.$content.hide();
			this.$content.eq(n).show()
		}
	});
	new o("#Ewm1");
	new o("#Ewm2")
});
define("home/FamilyActivity", function(e, t, n) {
	var i = e("basic/jquery");
	var r = e("basic/oop/Class");
	var s = e("basic/Util");
	var a = e("home/tpl");
	var o = e("common/NaviTip");
	var l = r.extend({
		init: function() {
			this.$elem = i("#familyReco");
			this.$liveList = this.$elem.find("ul");
			this.getData();
			this.bindEvent()
		},
		getData: function() {
			var e = this;
			var t = "/familyRoom/homeLive.do";
			i.get(t, i.proxy(function(e) {
				if (e.status == "1") {
					if (e.total > 0) {
						var t = e.liveFamilyRooms.length;
						if (t) {
							this.render(e.liveFamilyRooms)
						}
					} else {
						this.$elem.hide()
					}
				} else {
					this.$elem.hide()
				}
			}, this))
		},
		render: function(e) {
			var t = [];
			i.each(e, function(e, n) {
				if (e > 3) {
					return
				}
				n["num"] = e + 1;
				n["time"] = n.live_id <= 0 ? s.formatDate(n.live_begin, "yyyy/MM/dd HH:mm") : n.liveTime;
				t.push(s.formatTemplate(a.familyRecommend, n))
			});
			this.$liveList.html(t.join(""))
		},
		renderTip: function(e, t) {
			var n = t.html();
			t.html(n + s.formatTemplate(e, {}))
		},
		bindEvent: function() {
			var e = this;
			e.$elem.on("mouseenter", "li", function() {
				var e = i(this),
					t = e.find(".family-recommend-icon");
				if (!e.hasClass("hover")) {
					e.addClass("hover");
					t.animate({
						right: "0px"
					})
				}
			});
			e.$elem.on("mouseleave", "li", function() {
				var e = i(this),
					t = e.find(".family-recommend-icon");
				if (e.hasClass("hover")) {
					e.removeClass("hover");
					t.animate({
						right: "-33px"
					})
				}
			});
			i("#familyReco").delegate(".js-family-follow", "click", function(e) {
				var t = i(this).attr("data-roomid"),
					n = i(this);
				if (CONFIG.islogined) {
					i.post("/familyRoom/favorite.do", {
						roomId: t
					}, function(e) {
						if (e.status == 1) {
							n.addClass("hidden");
							n.parent().find(".js-family-unfollow").removeClass("hidden")
						} else {
							o("error", e.errorMsg)
						}
					})
				} else {
					s.login()
				}
			});
			i("#familyReco").delegate(".js-family-unfollow", "click", function(e) {
				var t = i(this).attr("data-roomid"),
					n = i(this);
				if (CONFIG.islogined) {
					i.post("/familyRoom/unfavorite.do", {
						roomId: t
					}, function(e) {
						if (e.status == 1) {
							n.addClass("hidden");
							n.parent().find(".js-family-follow").removeClass("hidden")
						} else {
							o("error", e.errorMsg)
						}
					})
				} else {
					s.login()
				}
			})
		}
	});
	var c = new l
});
define("home/GetBobi", function(e, t, n) {
	var i = e("basic/jquery");
	var r = e("basic/oop/Class");
	var s = e("basic/Util");
	var a = e("home/tpl");
	var o = e("basic/Observer");
	var l = e("common/FloatWin/FloatWin");
	var c = r.extend({
		init: function() {
			this.$elem = i("#getBobi").show();
			this.$content = this.$elem.find(".js-content");
			this.$info = this.$elem.find(".js-info");
			this.bindEvent()
		},
		bindEvent: function() {
			if (location.href.indexOf("getbobi") != -1) {
				this.onUnFold()
			}
			this.$elem.delegate(".js-unfold", "click", i.proxy(this.onUnFold, this)).delegate(".js-fold", "click", i.proxy(this.onFold, this)).delegate(".js-content li .js-hover", "hover", i.proxy(this.onHover, this)).delegate(".js-receive", "click", i.proxy(this.onReceive, this))
		},
		onUnFold: function(e) {
			e && e.preventDefault();
			if (!CONFIG["islogined"]) {
				this.render()
			} else {
				this.getData()
			}
		},
		onFold: function() {
			this.$info.hide()
		},
		onHover: function(e) {
			var t = i(e.currentTarget);
			if (e.type === "mouseenter") {
				t.siblings(".js-detail").show()
			} else {
				t.siblings(".js-detail").hide()
			}
		},
		onReceive: function(e) {
			var t = i(e.currentTarget);
			var n = this;
			this.type = t.attr("data-type");
			var r = "/captcha?captchaToken=" + n.captchaToken;
			var s = new l.CheckCodeWin({
				url: r + "&timestamp=" + (new Date).getTime(),
				confirmFunc: function(e, a, o) {
					var l = e.val();
					if (i.trim(l) == "") {
						e.focus();
						return
					}
					var c = "/user/rookieGuideReward.do?type=" + n.type + "&captchaToken=" + n.captchaToken + "&captchaCode=" + l;
					i.ajax({
						type: "POST",
						url: c,
						dataType: "json",
						success: function(e) {
							if (e.status == 1) {
								s.hide();
								t.hide();
								t.siblings(".js-received").show();
								n.onLoginCoin()
							} else {
								a.show();
								o.attr("src", r + "&timestamp=" + (new Date).getTime())
							}
						},
						error: function() {}
					})
				}
			});
			s.show()
		},
		getData: function() {
			if (i.isPlainObject(this.Request) && this.Request.readyState !== 4) {
				return
			}
			var e = this;
			this.Request = i.ajax({
				type: "POST",
				url: "/user/listRookieGuide.do",
				dataType: "json",
				success: function(t) {
					e.rookieGuide = i.extend(true, e.rookieGuide, t.rookieGuide);
					e.render();
					e.captchaToken = t.captchaToken
				},
				error: function() {}
			})
		},
		render: function() {
			var e = [];
			var t = this.rookieGuide;
			var n = ["REGISTER", "ENTER_ROOM", "GROUP_CHAT", "JOIN_FAMILY"];
			for (var r = 0; r < n.length; r++) {
				delete t[n[r]]
			}
			var o = 0;
			i.each(t, function(t, n) {
				e.push(s.formatTemplate(a.getBobi, i.extend(n, {
					key: t,
					index: o
				})));
				o++
			});
			this.$content.html(e.join(""));
			this.$info.show()
		},
		onLoginCoin: function() {
			var e = this.rookieGuide[this.type].cCurrency;
			if (e) {
				this.buySuccess = new o.Publisher("buy:success");
				this.buySuccess.deliver(s.formatNumber(s.recoverNumber(i("#loginCoin").text()) + e))
			}
		},
		rookieGuide: {
			REGISTER: {
				img: 1,
				title: "\u5b8c\u6210\u6ce8\u518c",
				cCurrency: 5,
				quota: 0,
				total: 1,
				receiveStatus: 0
			},
			ENTER_ROOM: {
				img: 2,
				title: "\u8fdb\u51653\u6b21\u76f4\u64ad\u95f4\u89c2\u770b\u76f4\u64ad",
				cCurrency: 5,
				quota: 0,
				total: 3,
				receiveStatus: 0
			},
			GROUP_CHAT: {
				img: 3,
				title: "\u5728\u76f4\u64ad\u95f4\u516c\u804a\u533a\u53d110\u6761\u6d88\u606f",
				cCurrency: 5,
				quota: 0,
				total: 10,
				receiveStatus: 0
			},
			FOLLOW: {
				img: 4,
				title: "\u9996\u6b21\u5173\u6ce83\u4e2a\u4e3b\u64ad",
				cCurrency: 5,
				quota: 0,
				total: 3,
				receiveStatus: 0
			},
			SEND_FREE_GIFT: {
				img: 6,
				title: "\u9001\u4e3b\u64ad10\u6735\u73ab\u7470\u82b1",
				cCurrency: 5,
				quota: 0,
				total: 10,
				receiveStatus: 0
			},
			LOLLIPOP: {
				img: 7,
				title: "\u9001\u4e3b\u64ad5\u652f\u68d2\u68d2\u7cd6",
				cCurrency: 10,
				quota: 0,
				total: 5,
				receiveStatus: 0
			},
			ORDER_SONG: {
				img: 8,
				title: "\u6210\u529f\u70b91\u6b21\u6b4c\uff0c\u5e76\u88ab\u4e3b\u64ad\u63a5\u53d7",
				cCurrency: 10,
				quota: 0,
				total: 1,
				receiveStatus: 0
			},
			TAKE_SOFA: {
				img: 9,
				title: "\u6210\u529f\u62a21\u6b21\u6c99\u53d1",
				cCurrency: 10,
				quota: 0,
				total: 1,
				receiveStatus: 0
			},
			JOIN_FAMILY: {
				img: 10,
				title: "\u9996\u6b21\u52a0\u51651\u4e2a\u5bb6\u65cf",
				cCurrency: 5,
				quota: 0,
				total: 1,
				receiveStatus: 0
			},
			APP_FIRST: {
				img: 11,
				title: "\u4e0b\u8f7d\u5e76\u767b\u9646\u65b0\u7248\u624b\u673a\u5ba2\u6237\u7aef",
				cCurrency: 10,
				quota: 0,
				total: 1,
				receiveStatus: 0
			}
		}
	});
	new c
});
define("home/HomeLogin", function(e, t, n) {
	var i = e("basic/jquery");
	var r = e("basic/oop/Class");
	var s = e("basic/Util");
	var a = e("common/HoverSelect");
	var o = e("common/Login/LoginSuggest");
	var l = e("home/tpl");
	var c = r.extend({
		init: function() {
			this.$elem = i("#homeLogin");
			this.$inputElem = i("#loginUsername");
			this.$passwordElem = i("#loginPassword");
			this.$errorTip = this.$elem.find(".js-errorTip");
			this.$elem.find(".js-loginUrl").val(location.href);
			this.$elem.find(".js-qq").attr("href", "http://reg.163.com/outerLogin/oauth2/connect.do?target=1&domains=bobo.com,163.com&product=bobo&url=" + location.href + "&url2=" + location.href);
			this.$elem.find(".js-sina").attr("href", "http://reg.163.com/outerLogin/oauth2/connect.do?target=3&domains=bobo.com,163.com&product=bobo&url=" + location.href + "&url2=" + location.href);
			this.bindEvent()
		},
		bindEvent: function() {
			new a({
				$hoverSrc: this.$elem.find(".js-loginCenter"),
				$hoverTarget: this.$elem.find(".js-loginList"),
				enterFunc: i.proxy(function() {
					this.$elem.find(".js-loginInfo").hide()
				}, this),
				leaveFunc: i.proxy(function() {
					this.$elem.find(".js-loginInfo").show()
				}, this)
			});
			new a({
				$hoverSrc: this.$elem.find(".js-forget"),
				$hoverTarget: this.$elem.find(".js-forgetTip")
			});
			if (!CONFIG.islogined) {
				this.$elem.find("form").submit(i.proxy(this.onSubmit, this));
				this.onLoginSuggest();
				this.onPlaceHolder()
			}
		},
		onLoginSuggest: function() {
			var e = document.getElementById("loginUsername"),
				t = document.getElementById("loginPassword");
			new o(e, function() {
				i(t).focus()
			}, true)
		},
		onPlaceHolder: function() {
			setTimeout(i.proxy(function() {
				this.$inputElem.focus()
			}, this), 0);
			if ("placeholder" in document.createElement("input")) {
				this.$elem.find(".js-defaultText").hide();
				return
			}
			this.$elem.delegate(".js-defaultText", "click", function() {
				i(this).siblings("input").focus()
			}).delegate("input", "keyup", function() {
				if (i.trim(i(this).val()) === "") {
					i(this).siblings(".js-defaultText").show()
				} else {
					i(this).siblings(".js-defaultText").hide()
				}
			})
		},
		onCheckVal: function() {
			var e = i.trim(this.$inputElem.val());
			var t = this.$passwordElem.val();
			if (e === "") {
				this.showError("\u7528\u6237\u540d\u4e0d\u80fd\u4e3a\u7a7a");
				return false
			} else if (!s.validateEmail(e)) {
				this.showError("\u90ae\u7bb1\u683c\u5f0f\u6709\u8bef");
				return false
			} else if (t === "") {
				this.showError("\u5bc6\u7801\u4e0d\u80fd\u4e3a\u7a7a");
				return false
			}
			this.hideError();
			return true
		},
		onSubmit: function(e) {
			if (!this.onCheckVal()) {
				e.preventDefault()
			}
		},
		showError: function(e) {
			this.$errorTip.show().text(e)
		},
		hideError: function() {
			this.$errorTip.hide()
		}
	});
	new c
});
define("home/HomeSlide", function(e, t, n) {
	var i = e("basic/jquery");
	var r = e("basic/oop/Class");
	var s = e("basic/Util");
	var a = e("common/RollSlider");
	var o = e("home/tpl");
	var l = r.extend({
		init: function() {
			this.$elem = i("#homeSlide").data("index", 1);
			this.$container = this.$elem.find(".js-container");
			this.http = CONFIG.mode == "3" ? "http://bobo.com" : "http://223.252.196.214";
			this.inited = false;
			this.getData()
		},
		getData: function() {
			var e = this.http + "/spe-data/index/head-pic.js";
			i.getScript(e, i.proxy(function() {
				var e = headPic;
				if (e.length) {
					this.render(e);
					this.initSlide()
				}
			}, this))
		},
		render: function(e) {
			var t = [],
				n = [];
			i.each(e, function(e, i) {
				i.Util = s;
				t.push(s.formatTemplate(o.homeSlide, i));
				n.push('<a href="javascript:;"></a>')
			});
			this.$elem.find(".js-bgContainer").html(t.join(""));
			this.$elem.find(".js-tab p").html(n.join("")).find("a").first().addClass("cur");
			this.initBgWidth()
		},
		initBgWidth: function() {
			this.$elem.find(".js-bgItem").width(this.$elem.width());
			i(window).bind("resize.homeSlide", i.proxy(function(e) {
				if (this.inited) this.resetSlide()
			}, this));
			var t = e("basic/Observer"),
				n = this;
			t.subscribe({
				eventName: "home:resetSlide",
				func: function(e) {
					n.resetSlide()
				}
			});
			setTimeout(i.proxy(function() {
				this.inited = true
			}, this), 200)
		},
		resetSlide: function() {
			setTimeout(i.proxy(function() {
				this.$elem.find(".js-bgItem").width(this.$elem.width());
				this.Slider.slideWidth = this.$elem.width();
				this.$elem.find(".js-bgContainer").css({
					left: -this.Slider.current * this.Slider.slideWidth
				}).width(9999999)
			}, this), 200)
		},
		initSlide: function() {
			var e = true;
			if (typeof window.isAutoPlay != "undefined") {
				e = window.isAutoPlay
			}
			var t = {
				autoplay: e,
				interval: 8e3,
				pauseOnHover: true,
				tabClass: "cur",
				type: "roll",
				reel: ".js-bgContainer",
				slideItem: ".js-bgItem",
				pageItem: ".js-tab a",
				prevBtn: ".js-prev",
				nextBtn: ".js-next",
				duration: 700,
				rollCycle: true,
				rollCallBack: i.proxy(this.rollCallBack, this)
			};
			this.Slider = new a(this.$elem, t)
		},
		rollCallBack: function(e) {
			this.$elem.data("index", e)
		}
	});
	new l
});
define("home/ImageWall", function(e, t, n) {
	var i = e("basic/jquery");
	var r = e("basic/oop/Class");
	var s = e("basic/Util");
	var a = e("basic/Observer");
	var o = e("home/tpl");
	var l = r.extend({
		init: function() {
			this.$elem = i("#imageWall");
			this.$homeLayout = i(".js-homeLayout");
			this.$photoAlbum = this.$elem.find(".js-photoAlbum");
			this.resetHomeSlide = new a.Publisher("home:resetSlide");
			this.show();
			this.bindEvent()
		},
		getData: function() {
			i.get("/getImageWall", i.proxy(function(e) {
				var e = e.imageWalls;
				if (e.length) {
					this.render(e);
					this.show()
				}
			}, this))
		},
		render: function(e) {
			this.$elem.append(s.formatTemplate(o.imageWall, {
				data: e
			}));
			this.bindEvent()
		},
		bindEvent: function() {
			this.$elem.delegate(".js-verticalFold", "click", i.proxy(this.verticalFold, this)).delegate(".js-verticalUnFold", "click", i.proxy(this.verticalUnFold, this)).delegate(".js-levelFold", "click", i.proxy(this.levelFold, this)).delegate(".js-levelUnFold", "click", i.proxy(this.levelUnFold, this))
		},
		show: function() {
			this.timeout = setTimeout(i.proxy(function() {
				if (i(window).width() < 1600) {
					this.$elem.find(".js-verticalFold").click()
				}
			}, this), 2e3)
		},
		verticalFold: function() {
			this.initHeight = this.$photoAlbum.height();
			this.$elem.find(".js-verticalFold").hide();
			this.timeout && clearTimeout(this.timeout);
			this.$photoAlbum.animate({
				height: 0
			}, 1200, i.proxy(function() {
				this.$elem.find(".js-verticalUnFold").show();
				this.$photoAlbum.height(this.initHeight).hide();
				this.$photoAlbum.data("showed", false)
			}, this))
		},
		verticalUnFold: function() {
			this.$elem.find(".js-verticalUnFold").hide();
			this.initHeight = this.$photoAlbum.height();
			this.$photoAlbum.height(0).show().animate({
				height: this.initHeight
			}, 1200, i.proxy(function() {
				this.$elem.find(".js-verticalFold").show();
				this.$photoAlbum.data("showed", true)
			}, this))
		},
		levelFold: function() {
			this.$elem.find(".js-levelFold").hide();
			this.$photoAlbum.css({
				right: 0
			}).animate({
				right: -300
			}, 1200, i.proxy(function() {
				this.$elem.find(".js-levelUnFold").show();
				this.resetSlide();
				this.$photoAlbum.data("showed", false)
			}, this));
			this.$homeLayout.animate({
				width: "100%"
			}, 1200);
			i("#homeSlide").find(".js-bgItem").eq(i("#homeSlide").data("index")).animate({
				width: "+=300"
			}, 1200);
			i(".toTop,.fresh-open,.fresh").animate({
				right: 10
			}, 1200)
		},
		levelUnFold: function() {
			this.$elem.find(".js-levelUnFold").hide();
			this.$photoAlbum.show().css({
				right: -300
			}).animate({
				right: 0
			}, 1200, i.proxy(function() {
				this.$elem.find(".js-levelFold").show();
				this.resetSlide();
				this.$photoAlbum.data("showed", true)
			}, this));
			var e = i(window).width() - this.$photoAlbum.width();
			this.$homeLayout.animate({
				width: e
			}, 1200);
			i("#homeSlide").find(".js-bgItem").eq(i("#homeSlide").data("index")).animate({
				width: e
			}, 1200);
			i(".toTop,.fresh-open,.fresh").animate({
				right: 311
			}, 1200)
		},
		resetSlide: function() {
			this.resetHomeSlide.deliver()
		}
	});
	new l
});
define("home/News", function(e, t, n) {
	var i = e("basic/jquery");
	var r = e("basic/oop/Class");
	var s = e("basic/Util");
	var a = e("home/tpl");
	var o = r.extend({
		init: function() {
			this.$elem = i("#homeNews");
			this.$container = this.$elem.find(".js-container");
			this.http = CONFIG.mode == "3" ? "http://bobo.com" : "http://123.126.62.157";
			this.getData()
		},
		getData: function() {
			var e = "http://bobo.com/spe-data/index/bobo-news.js";
			i.getScript(e, i.proxy(function() {
				var e = headInfo;
				if (e.length) {
					this.render(e);
					this.$elem.show()
				}
			}, this))
		},
		render: function(e) {
			this.$container.html(s.formatTemplate(a.homeNews, {
				data: e,
				Util: s
			}))
		}
	});
	new o
});
define("home/RankTab", function(e, t, n) {
	var i = e("basic/jquery");
	var r = e("basic/oop/Class");
	var s = e("basic/Util");
	var a = e("home/tpl");
	var o = r.extend({
		init: function(e) {
			this.$elem = i(e);
			this.$content = this.$elem.find(".js-content");
			this.period = 1;
			this.url = this.$elem.attr("data-url");
			this.type = this.$elem.attr("data-type");
			this.bindEvent();
			this.getData()
		},
		bindEvent: function() {
			this.$elem.delegate(".js-tab", "click", i.proxy(this.onTab, this))
		},
		onTab: function(e) {
			e.preventDefault();
			var t = i(e.currentTarget);
			if (t.hasClass("cur")) return;
			t.addClass("cur").siblings().removeClass("cur");
			this.period = t.attr("data-period");
			this.getData()
		},
		getData: function() {
			if (i.isPlainObject(this.Request) && this.Request.readyState !== 4) {
				return
			}
			var e = this;
			i.ajax({
				type: "POST",
				url: this.url,
				data: {
					period: this.period
				},
				dataType: "json",
				success: function(t) {
					e.render(t.data.rankUser)
				},
				error: function() {}
			})
		},
		render: function(e) {
			var t = [],
				n = this;

			function r(e) {
				return s.getImageUrl(e, 68)
			}
			i.each(e, function(e, o) {
				o.countNick = s.cutString(o.userWrapInfo.nick, 0, 7);
				o.format = r;
				t.push(s.formatTemplate(a.rankItem, i.extend(o, {
					index: e + 1
				}, {
					type: n.type
				})))
			});
			this.$content.html(t.join(""))
		}
	});
	new o("#starRank");
	new o("#wealthRank")
});
define("home/Resolution", function(e, t, n) {
	var i = e("basic/jquery");
	var r = e("basic/oop/Class");
	var s = i(document.body);
	var a = r.extend({
		init: function() {
			this.$homeLayout = i(".js-homeLayout");
			this.$photoAlbum = i(".js-photoAlbum").data("showed", true);
			this.bindEvent();
			s.show();
			this.resolution(i(window).width())
		},
		bindEvent: function() {
			var e = this;
			var t = function() {
				i(window).bind("resize.Resolution", function() {
					setTimeout(function() {
						e.resolution(i(window).width())
					}, 5);
					i(window).unbind("resize.Resolution");
					setTimeout(t, 100)
				})
			};
			t()
		},
		sLayout: function() {
			s.addClass("home-s-layout");
			s.find(".js-sExpand").unbind().click(function() {
				i(".conR").hide();
				s.find(".js-sExpand").hide();
				s.find(".js-fExpand").show()
			});
			s.find(".js-fExpand").unbind().click(function() {
				i(".conR").show();
				s.find(".js-sExpand").show();
				s.find(".js-fExpand").hide()
			});
			if (this.$photoAlbum.data("showed")) {
				this.$photoAlbum.show();
				i(".js-verticalFold").show();
				i(".js-verticalUnFold").hide()
			} else {
				this.$photoAlbum.hide();
				i(".js-verticalFold").hide();
				i(".js-verticalUnFold").show()
			}
			i(".js-levelFold, .js-levelUnFold").hide()
		},
		mLayout: function() {
			s.addClass("home-m-layout");
			i(".conR").show();
			if (this.$photoAlbum.data("showed")) {
				this.$photoAlbum.show();
				i(".js-verticalFold").show();
				i(".js-verticalUnFold").hide()
			} else {
				this.$photoAlbum.hide();
				i(".js-verticalFold").hide();
				i(".js-verticalUnFold").show()
			}
			i(".js-levelFold, .js-levelUnFold").hide()
		},
		lLayout: function() {
			s.addClass("home-l-layout");
			i(".conR").show();
			if (this.$photoAlbum.data("showed")) {
				this.$photoAlbum.show();
				i(".js-levelFold").show();
				i(".js-levelUnFold").hide();
				this.$homeLayout.width(i(window).width() - this.$photoAlbum.width());
				i(".toTop,.fresh-open,.fresh").css({
					right: 311
				})
			} else {
				this.$photoAlbum.hide();
				i(".js-levelFold").hide();
				i(".js-levelUnFold").show();
				this.$homeLayout.width("100%");
				i(".toTop,.fresh-open,.fresh").css({
					right: 10
				})
			}
			i(".js-verticalUnFold, .js-verticalFold").hide()
		},
		resolution: function(e) {
			s.removeAttr("class");
			this.$homeLayout.width("auto");
			this.$photoAlbum.stop(true, true).removeAttr("style");
			i(".toTop,.fresh-open,.fresh").css({
				right: 10
			});
			if (e <= 1200) this.sLayout();
			else if (e > 1200 && e < 1600) this.mLayout();
			else if (e >= 1600) this.lLayout();
			if (i.browser.msie && parseInt(i.browser.version) <= 7) {}
		}
	});
	new a
});
define("home/UserCard", function(e, t, n) {
	var i = e("basic/jquery");
	var r = e("basic/oop/Class");
	var s = e("basic/Util");
	var a = e("home/tpl");
	var o = r.extend({
		init: function(e) {
			this.$elem = i(e);
			this.$content = this.$elem.find(".js-content");
			this.$prev = this.$elem.find(".js-prev");
			this.$next = this.$elem.find(".js-next");
			this.$more = this.$elem.find(".js-more");
			this.$none = this.$elem.find(".js-none");
			this.url = "/getUserCardRightMyFollow";
			this.pageNo = 1;
			this.go = 1;
			this.type = "tab";
			this.bindEvent()
		},
		bindEvent: function() {
			this.$elem.delegate(".js-tab", "click", i.proxy(this.onTab, this)).delegate(".js-prev, .js-next", "click", i.proxy(this.onPage, this))
		},
		onTab: function(e) {
			e.preventDefault();
			var t = i(e.currentTarget);
			if (t.hasClass("cur")) return;
			t.addClass("cur").siblings().removeClass("cur");
			t.attr("data-more") == "true" ? this.$more.show() : this.$more.hide();
			this.url = t.attr("data-href");
			this.pageNo = 1;
			this.type = "tab";
			this.$content.attr("data-keyfrom", t.attr("data-from"));
			this.getData()
		},
		onPage: function(e) {
			e.preventDefault();
			var t = i(e.currentTarget);
			this.go = parseInt(t.attr("data-no"));
			this.pageNo += this.go;
			this.type = "page";
			this.getData()
		},
		getData: function() {
			if (i.isPlainObject(this.Request) && this.Request.readyState !== 4) {
				return
			}
			var e = {
				pageNo: this.pageNo
			};
			var t = this;
			this.Request = i.ajax({
				type: "POST",
				url: this.url,
				data: e,
				dataType: "json",
				success: function(e) {
					t.onDataBack(e)
				},
				error: function() {}
			})
		},
		onDataBack: function(e) {
			var t = e[this.key[this.url]];
			if (!t || !t.dataList || !t.dataList.length) {
				this.$content.empty();
				this.$none.show();
				this.$prev.addClass("hidden");
				this.$next.addClass("hidden");
				return
			}
			this.$none.hide();
			this.pageNo = t.pageNo;
			this.isLastPage = t.full;
			this.render(t.dataList)
		},
		render: function(e) {
			var t = [];
			i.each(e, function(e, n) {
				n.cover && (n.cover = s.getImageUrl(n.cover, 180, 120));
				n.title = s.anchor[n.anchorLevel];
				t.push(s.formatTemplate(a.userCard, n))
			});
			if (this.type == "tab") {
				this.$content.html(t.join(""));
				if (!i.browser.msie || i.browser.msie && parseInt(i.browser.version, 10) > 8) {
					this.$content.hide().fadeIn(700)
				}
			} else if (this.type == "page") {
				this.$content.html(t.join("")).find("li").css("visibility", "hidden");
				var n = this.$content.find("li");
				if (this.go == -1) {
					n.each(function(e) {
						var t = i(this);
						setTimeout(function() {
							t.css("visibility", "visible")
						}, e * 80)
					})
				} else {
					n.each(function(e) {
						var t = n.eq(n.length - (e + 1));
						setTimeout(function() {
							t.css("visibility", "visible")
						}, e * 80)
					})
				}
			}
			this.pageNo == 1 ? this.$prev.addClass("hidden") : this.$prev.removeClass("hidden");
			this.isLastPage ? this.$next.addClass("hidden") : this.$next.removeClass("hidden")
		},
		key: {
			"/getUserCardRightMyFollow": "userCardRightMyFollow",
			"/getUserRecently": "recentRooms",
			"/newAnchor": "newAnchors"
		}
	});
	new o("#userCard")
});
define("home/YearCard", function(e, t, n) {
	var i = e("basic/jquery");
	var r = e("basic/oop/Class");
	var s = e("basic/Util");
	var a = e("common/FloatWin/FloatWin");
	var o = r.extend({
		init: function() {
			var e = this;
			i.get("/memoryHome", function(t) {
				if (t.status == 1 && t.isMemory) {
					e.show()
				}
			})
		},
		preRender: function() {
			return {
				content: '<h2></h2><a class="btn-huigu png24" href="/special/memory/memory_user.html" target="_blank"></a>'
			}
		},
		postRender: function(e) {
			e.addClass("year-card-layer");
			this.$elem = e;
			e.find("h2").text(CONFIG["nick"]);
			this.$elemHead = this.$elem.find(".dialogLayer-hd");
			this.$elemHead.find(".js-close").addClass("btn-card-close").removeClass("btn-close");
			e.find(".btn-huigu").click(function() {
				window.location.reload()
			})
		},
		show: function() {
			a.PopWin({
				preRender: i.proxy(this.preRender, this),
				postRender: i.proxy(this.postRender, this)
			}).show()
		}
	});
	new o
});
define("home/Yiren", function(e, t, n) {
	var i = e("basic/jquery");
	var r = e("basic/oop/Class");
	var s = e("basic/Util");
	var a = e("home/tpl");
	var o = r.extend({
		init: function() {
			this.$elem = i("#homeYiren");
			this.$container = this.$elem.find(".js-container");
			this.$content = this.$elem.find(".js-content");
			this.http = CONFIG.mode == "3" ? "http://bobo.com" : "http://123.126.62.157";
			this.getData()
		},
		getData: function() {
			var e = this.http + "/spe-data/index/anchor-recommend.js";
			i.getScript(e, i.proxy(function() {
				var e = headAchorRecommend;
				if (e.list.length) {
					this.render(e.list);
					this.initSlide()
				}
			}, this))
		},
		render: function(e) {
			var t = [];
			i.each(e, function(e, n) {
				t.push(s.formatTemplate(a.homeYiren, n))
			});
			this.$content.html(t.join(""))
		},
		initSlide: function() {
			var e = this.$content.find("li");
			var t = e.clone();
			this.$content.append(t);
			this.$panels = this.$content.find("li");
			this.totalPanels = this.$panels.length;
			this.movingDistance = this.$panels.eq(0).outerWidth(true);
			this.othersW = this.$panels.eq(0).width();
			this.othersH = this.$panels.eq(0).height();
			this.othersImgW = this.$panels.eq(0).find("img").width();
			this.othersImgH = this.$panels.eq(0).find("img").height();
			this.curWidth = 150;
			this.curHeight = 205;
			this.curImgWidth = 150;
			this.curImgHeight = 195;
			this.currentlyMoving = false;
			this.$content.css("width", this.movingDistance * this.$panels.length + (this.curWidth - this.othersW)).css({
				position: "relative",
				left: 0
			});
			this.$container.css({
				overflow: "hidden",
				position: "relative"
			});
			this.curPanel = this.totalPanels / 2;
			this.$content.css({
				left: -(this.curPanel - 1) * this.movingDistance
			});
			this.bindEvent();
			this.growBigger(this.$panels.eq(this.curPanel));
			setTimeout(i.proxy(function() {
				this.$elem.show()
			}, this), 500)
		},
		bindEvent: function() {
			var e = this;
			this.$panels.eq(this.curPanel + 1).click(function() {
				e.change(true);
				return false
			});
			this.$panels.eq(this.curPanel - 1).click(function() {
				e.change(false);
				return false
			});
			this.$elem.delegate(".js-next", "click", i.proxy(this.change, this, true)).delegate(".js-prev", "click", i.proxy(this.change, this, false))
		},
		growBigger: function(e) {
			i(e).addClass("cur").animate({
				width: this.curWidth,
				height: this.curHeight
			}).siblings().removeClass("cur").end().find("img").animate({
				width: this.curImgWidth,
				height: this.curImgHeight
			})
		},
		change: function(e) {
			if (!this.currentlyMoving) {
				this.currentlyMoving = true;
				var t = e ? this.curPanel + 1 : this.curPanel - 1;
				var n = this.$content.css("left");
				var i = e ? parseFloat(n, 10) - this.movingDistance : parseFloat(n, 10) + this.movingDistance;
				var r = this;
				this.returnToNormal(this.$panels.eq(this.curPanel));
				this.growBigger(this.$panels.eq(t));
				this.curPanel = t;
				this.$content.stop().animate({
					left: i
				}, function() {
					r.currentlyMoving = false;
					r.isEdge()
				});
				this.$panels.eq(this.curPanel + 1).unbind().click(function() {
					r.change(true);
					return false
				});
				this.$panels.eq(this.curPanel - 1).unbind().click(function() {
					r.change(false);
					return false
				});
				this.$panels.eq(this.curPanel).unbind()
			}
		},
		returnToNormal: function(e) {
			i(e).animate({
				width: this.othersW,
				height: this.othersH
			}).find("img").animate({
				width: this.othersImgW,
				height: this.othersImgH
			})
		},
		isEdge: function() {
			if (this.curPanel == this.totalPanels - 1) {
				this.$panels.eq(this.curPanel).css({
					width: this.othersImgW,
					height: this.othersImgH
				}).end().find("img").css({
					width: this.othersImgW,
					height: this.othersImgH
				});
				this.curPanel = this.totalPanels / 2 - 1;
				this.$panels.eq(this.curPanel).addClass("cur").css({
					width: this.curWidth,
					height: this.curHeight
				}).siblings().removeClass("cur").end().find("img").css({
					width: this.curImgWidth,
					height: this.curImgHeight
				});
				this.$content.css("left", -(this.curPanel - 1) * this.movingDistance)
			}
			if (this.curPanel == 0) {
				this.$panels.eq(this.curPanel).css({
					width: this.othersImgW,
					height: this.othersImgH
				}).end().find("img").css({
					width: this.othersImgW,
					height: this.othersImgH
				});
				this.curPanel = this.totalPanels / 2;
				this.$panels.eq(this.curPanel).addClass("cur").css({
					width: this.curWidth,
					height: this.curHeight
				}).siblings().removeClass("cur").end().find("img").css({
					width: this.curImgWidth,
					height: this.curImgHeight
				});
				this.$content.css("left", -(this.curPanel - 1) * this.movingDistance)
			}
		}
	});
	new o
});
define("home/ZhuboGold", function(e, t, n) {
	var i = e("basic/jquery");
	var r = e("basic/oop/Class");
	var s = e("basic/Util");
	var a = e("common/RollSlider");
	var o = e("home/tpl");
	var l = r.extend({
		init: function() {
			this.$elem = i("#zhuboGold");
			this.$container = this.$elem.find(".js-container");
			this.$content = this.$elem.find(".js-content");
			this.http = CONFIG.mode == "3" ? "http://bobo.com" : "http://123.126.62.157";
			this.getData()
		},
		getData: function() {
			var e = this.http + "/spe-data/index/anchor-recommend-golden.js";
			i.getScript(e, i.proxy(function() {
				var e = goldenAchorRecommend;
				if (e.list.length) {
					this.render(e.list);
					this.initSlide()
				}
			}, this))
		},
		render: function(e) {
			var t = [];
			i.each(e, function(e, n) {
				n["Util"] = s;
				t.push(s.formatTemplate(o.homeZhubo, n))
			});
			this.$content.html(t.join(""))
		},
		initSlide: function() {
			var e = this.$elem.find(".js-zhubo-slider-item").length;
			this.$elem.find(".js-total").html(e);
			setTimeout(i.proxy(function() {
				this.$elem.show()
			}, this), 500);
			var t = {
				autoplay: true,
				interval: 2e3,
				pauseOnHover: true,
				rollCycle: true,
				type: "roll",
				reel: ".js-content",
				slideItem: ".js-zhubo-slider-item",
				prevBtn: ".js-prev",
				nextBtn: ".js-next",
				duration: 700,
				rollCycle: true,
				rollCallBack: i.proxy(this.rollCallBack, this)
			};
			this.Slider = new a(this.$elem, t)
		},
		rollCallBack: function(e) {
			this.$elem.find(".js-num").html(e % (this.Slider.slideCount / 2) + 1)
		}
	});
	new l
});
define("home/neteaseLive", function(e, t, n) {
	var i = e("basic/jquery");
	var r = "http://swf.ws.126.net/bobo/player/liveplayer_1.3.swf";
	var s = "http://swf.ws.126.net/ad/AdvManager.swf";

	function a(e) {
		if (/(iPhone|iPod|Android)/gi.test(navigator.userAgent)) {
			document.write('<Video width="' + e.playerWidth + '" height="' + e.playerHeight + '" controls="controls" autoplay="autoplay" preload="auto"><source src="' + e.vid + '" type="video/mp4"></video>')
		} else if (/(iPad)/gi.test(navigator.userAgent)) {
			document.write('<Video width="' + e.playerWidth + '" height="' + e.playerHeight + '" controls="controls" autoplay="autoplay" preload="auto"><source src="' + e.vid + '" type="video/mp4"></video>')
		} else {
			var t = function(t) {
				var n = "";
				for (var s in e) {
					if (s == "playerWidth" || s == "playerHeight") {
						continue
					}
					n += s + "=" + e[s] + "&"
				}
				n = n.substring(0, n.length - 1);
				var a = "";
				if (t) {
					a = '<object  classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"  width="' + e.playerWidth + '" height="' + e.playerHeight + '" id="FPlayer" ><param value="#000000" name="bgcolor"><param value="true" name="allowFullScreen"><param value="always" name="allowscriptaccess"><param name="allownetworking" value="all" /><param name="wmode" value="opaque"><param value="' + r + '" name="movie"><param value="' + n + '" name="flashvars"></object>'
				} else {
					a = '<object width="' + e.playerWidth + '" height="' + e.playerHeight + '" id="FPlayer" data="' + r + '" type="application/x-shockwave-flash"><param value="#000000" name="bgcolor"><param value="true" name="allowFullScreen"><param value="always" name="allowscriptaccess"><param name="wmode" value="opaque"><param name="allownetworking" value="all" /><param value="' + n + '" name="flashvars"></object>'
				}
				i(".player_info").before(a)
			};
			var n = navigator.userAgent,
				s = n.toLowerCase().indexOf("msie") > -1 ? true : false;
			new t(s);
			o = new Date
		}
	}
	var o, l, c = "0";

	function u() {
		l = new Date
	}
	return a
});
define("home/top", function(e, t, n) {
	var i = e("basic/jquery");
	var r = e("basic/Util");
	var s = e("home/neteaseLive");

	function a() {
		this.init = function() {
			this.render(true, null);
			this.user_operate()
		};
		this.user_operate = function() {
			var e = i("#bar"),
				t = i("#manager_list");
			var n, r = this;
			e.on("click", "a", function(e) {
				i(".active").removeClass("active");
				i(this).addClass("active");
				switch (i(this).attr("class")) {
					case "star active":
						n = "crown";
						r.render(false, n);
						break;
					case "diamonds active":
						n = "diamonds";
						r.render(false, n);
						break;
					case "crown active":
						n = "star";
						r.render(false, n);
						break
				}
			});
			t.on("hover", ".consumer", function(e) {
				i(this).children("img").css("z-index", "20");
				i(this).parent().css("z-index", 12);
				i(this).children(".hover_details").show().width("136px")
			}).on("mouseout", ".consumer", function(e) {
				i(this).children(".hover_details").width("0");
				var t = i(this);
				if (!+[1]) {
					t.children("img").css("z-index", "0");
					t.parent().css("z-index", 10);
					t.children(".hover_details").css("display", "none");
					return
				}
				setTimeout(function() {
					t.children("img").css("z-index", "0");
					t.parent().css("z-index", 10);
					t.children(".hover_details").css("display", "none")
				}, 150)
			});
			i(".top-first").hover(function() {})
		};
		this.render = function(e, t) {
			this.deal_with_api(e, t)
		};
		this.deal_with_api = function(e, t) {
			var n = "";
			if (e && !t) {
				n = "http://www.bobo.com/spe-data/headline/anch_hour_rank_crown.js"
			} else {
				n = "http://www.bobo.com/spe-data/headline/anch_hour_rank_" + t + ".js"
			}
			this.get_data(n, function(e) {
				if (e["onGoing"] == 0) {
					this.get_data(this.create_api(t, "total", null), function(e) {
						this.refresh_html(e, "total")
					})
				} else {
					if (e["round"] != 1) {
						this.get_data(this.create_api(t, "hour", [e["round"], e["week"]]), function(e) {
							this.refresh_html(e, "hour")
						})
					} else {
						this.get_data(this.create_api(t, "total", null), function(e) {
							this.refresh_html(e, "total")
						})
					}
				}
			})
		};
		this.refresh_html = function(e, t) {
			if (e.length <= 0) {
				return
			}
			var e = this.sort_data(e, t);
			this.render_top_one(e, t);
			this.render_other_list(e, t);
			i(".home-top").show()
		};
		this.render_top_one = function(e, t) {
			var n = e[0].avatar,
				a = e[0].nick,
				o = e[0].anchor_level,
				l = e[0].live,
				c = e[0].room_id,
				u = e[0].user_num,
				f = e[0].consumerRank,
				d = e[0].isStar,
				p = "",
				h = f.length > 5 ? 5 : f.length;
			if (l == "1") {
				var m = i(".flashplayer");
				i(".content").hide();
				m.children(".enter").attr("href", "/" + c);
				m.children("object").remove();
				m.find(".anchor-name").html(r.cutString(a, 0, 9));
				m.find("em").eq(0).attr("class", "png24").addClass("medal-anchor" + o);
				s({
					playerWidth: "300",
					playerHeight: "225",
					sd: "http://extapi.live.netease.com/redirect/video/" + u,
					autoPlay: 1,
					mute: 0
				});
				m.show();
				if (d == 1) {
					m.find(".star_icon").show()
				} else {
					m.find(".star_icon").hide()
				}
				m.parent().height("266px")
			} else {
				i(".flashplayer").hide();
				i(".content").show();
				i(".content .user-avatar").attr("href", "/" + c);
				i(".content .user-avatar-inner > img").attr("src", n);
				i(".content .anchor-name").html(r.cutString(a, 0, 7));
				i(".content .anchor-name-link").attr("href", "/" + c);
				i(".content").find("em").eq(1).attr("class", "png24").addClass("medal-anchor" + o);
				if (d == 1) {
					i(".content").find("em").eq(2).attr("class", "png24").addClass("star_icon").show()
				} else {
					i(".content").find("em").eq(2).hide()
				}
				i(".content").parent().height("160px")
			}
			p += "<li class='first'><img class='png24' src='http://img1.cache.netease.com/bobo/image/top/manager_top.png'></li>";
			for (var g = 0; g < h; g++) {
				var v = f[g].avatar,
					y = f[g].wealth_level,
					b = f[g].nick,
					w = "";
				if (g >= 3) {
					w = "_right"
				} else {
					w = "_left"
				}
				p += "<li>" + "<a class='consumer'>" + "<img src=" + v + ">" + "<span class='hover_details " + w + "'>" + "<span class='consumer_name'>" + r.cutString(b, 0, 5) + "</span>" + "</span>" + "</a>" + "</li>"
			}
			p += "<li class='last'><a href='/" + c + "' class='png24' target='_blank'></a></li>";
			i(".last > a").attr("href", "/" + c);
			i("#manager_list > ul").html(p)
		};
		this.render_other_list = function(e, t) {
			if (e.length <= 0) {
				return
			}
			var n = "",
				s = e.length < 5 ? e.length : 5;
			for (var a = 1; a < s; a++) {
				var o = e[a].nick,
					l = e[a].avatar,
					c = e[a].anchor_level,
					u = e[a].live,
					f = e[a].isStar,
					d = e[a].room_id,
					p = f == 1 ? '<em class="png24 star_icon"></em>' : "";
				live_html = u == "1" ? "<div class='is-playing'>\u76f4\u64ad</div>" : "";
				n += "<li>" + "<a href='/" + d + "' target='_blank' class='user-avatar'>" + "<span class='user-avatar-inner'>" + "<img src='" + l + "'>" + live_html + "<em class='png24 avatar-mask'></em>" + "</span>" + "</a>" + "<a target='_blank' href='/" + d + "'><span class='anchor-name'>" + r.cutString(o, 0, 9) + "</span></a>" + "<em class='png24 medal-anchor" + c + "'></em>" + p + "</li>"
			}
			i(".top-other > ul").html(n)
		};
		this.get_data = function(e, t) {
			var n = this;
			i.ajax({
				url: e,
				dataType: "script",
				success: function() {
					if (e.indexOf("total") > -1) {
						t.call(n, anchorTotalRank)
					} else {
						t.call(n, anchorHourRank)
					}
				}
			})
		};
		this.sort_data = function(e) {
			var t = [];
			for (var n = 0; n < e.length; n++) {
				if (e[n].live == 1) {
					t.push(e[n])
				}
			}
			if (t.length < 5) {
				for (var n = 0; n < e.length; n++) {
					if (t.length == 5) {
						break
					}
					if (e[n].live == 0) {
						t.push(e[n])
					}
				}
			}
			return t
		};
		this.create_api = function(e, t, n) {
			var i = "http://www.bobo.com/spe-data/headline/",
				r = "";
			e = e || "crown";
			if (!n) {
				r = i + "anch_total_rank_" + e + ".js"
			} else {
				r = i + n[1] + "/anch_hour_rank_" + e + "_" + (n[0] - 1) + ".js"
			}
			return r
		}
	}
	if (!navigator.userAgent.match(/mobile/i)) {
		(new a).init()
	}
});
define("home/tpl", function(e, t, n) {
	var i = {
		userCard: '<li>                     <a href="/index.php/Show/index/roomnum/<%=roomId%>" class="js-play" target="_blank" data-keyfrom="recommend.pic">                        <img src="<%=cover%>" width="180" height="120">                        <%if(isLive){%><em class="png24 live-tip">\u76f4\u64ad</em><%}%>                        <p class="anchor-icon">                           <%if(anchorCategory==2 || anchorCategory==3){%>                             <em class="taiwan"><img src="http://img1.cache.netease.com/bobo/image/tai2.png"/></em>                           <%}%>                           <%if(anchorCategory==1){%>                             <em class="taiwan"><img src="http://img1.cache.netease.com/bobo/image/tai1.png"/></em>                           <%}%>                           <%if(iconList){%>                           <%for (var j=0;j<iconList.length;j++){%>                              <img class="fl png24" src="<%=iconList[j].iconUrl%>"/>                           <%}%>                           <%}%>                        </p>                        <p class="hot-anchor-hover"></p>                        <em class="anchor-play png24 icon-play"></em>                        <p class="hot-anchor-cover"></p>                        <p class="hot-anchor-fans"><em class="png24 icon-fansS"></em><%=onlineUserCount%></p>                     </a>                     <p class="anchor-name"><a href="/<%=roomId%>" target="_blank" data-keyfrom="recommend.word"><%=nick%></a><em title="<%=title%>" class="png24 medal-anchor<%=anchorLevel%>"></em></p>                     <%if (isLive){%>                     <p class="cGray"><em class="png24 icon-liveG"></em><%=timeInterval%></p>                     <%}%>                 </li>',
		rankItem: '<li <%if(index==1){%>class="home-rank-first clearfix"<%}%>><em class="home-rank-num"><%=index%></em>                  <%if(index==1){%>                  <a class="fl avatar-m-w js-box" href="javascript:;" title="<%=userWrapInfo.nick%>" userid="<%=userWrapInfo.userIdStr%>"><img src="<%=format(userWrapInfo.avatar)%>" /><em></em></a>                  <a title="<%=userWrapInfo.nick%>" class="fl js-box" href="javascript:;" userid="<%=userWrapInfo.userIdStr%>"><%=countNick%></a>                  <em class="png24 <%if(type=="star"){%>home-rank-anchor medal-anchor<%=userWrapInfo.anchorLevel%><%}else{%>home-rank-wealth medal-wealth<%=userWrapInfo.wealthLevel%><%}%>"></em>                  <%}else{%>                  <a title="<%=userWrapInfo.nick%>" class="fl js-box" href="javascript:;" userid=<%=userWrapInfo.userIdStr%>><%=userWrapInfo.nick%></a>                  <em class="png24 <%if(type=="star"){%>home-rank-anchor medal-anchor<%=userWrapInfo.anchorLevel%><%}else{%>home-rank-wealth medal-wealth<%=userWrapInfo.wealthLevel%><%}%>"></em>                  <%}%>               </li>',
		hotLive: '<ul>                  <%for(var i=0;i<anchorData.length;i++){%>                  <%var userWrapInfo = anchorData[i].userWrapInfo%>                  <%var unit = anchorData[i].unit%>                     <li <%if(i > 23){%>style="display:none;"<%}%>>                     <a href="/index.php/Show/index/roomnum/<%=unit.roomId%>" target="_blank" class="js-play" data-keyfrom="hotlive.pic">                        <img src="<%=Util.getImageUrl(unit.liveCoverUrl,180,120)%>" width="180" height="120" />                        <%if(unit.live>0){%><em class="png24 live-tip">\u76f4\u64ad</em><%}%>                        <p class="anchor-icon">                           <%if(unit.anchorCategory==2 || unit.anchorCategory==3){%>                              <em class="taiwan"><img src="http://img1.cache.netease.com/bobo/image/tai2.png"/></em>                           <%}%>                           <%if(unit.anchorCategory==1){%>                              <em class="taiwan"><img src="http://img1.cache.netease.com/bobo/image/tai1.png"/></em>                           <%}%>                           <%if(userWrapInfo.iconList){%>                           <%for (var j=0;j<userWrapInfo.iconList.length;j++){%>                              <img class="fl png24" src="<%=userWrapInfo.iconList[j].iconUrl%>"/>                           <%}%>                           <%}%>                        </p>                        <p class="hot-anchor-hover"></p>                        <em class="anchor-play png24 icon-play"></em>                        <p class="hot-anchor-cover"></p>                        <p class="hot-anchor-fans">                           <em class="png24 icon-fansS"></em><%=unit.onlineUserCount%>                        </p>                     </a>                     <p class="anchor-name"><a href="/<%=unit.roomId%>" target="_blank" data-keyfrom="hotlive.word"><%=userWrapInfo.nick%></a><em title="<%=Util.anchor[userWrapInfo.anchorLevel]%>" class="fr png24 medal-anchor<%=userWrapInfo.anchorLevel%>"></em></p>                        <%if(unit.live>0){%>                        <p class="cGray"><em class="png24 icon-liveG"></em><%=unit.timeInterval%></p>                        <%}%>                     </li>                  <%}%>                  </ul>                  <%if(anchorData.length>24){%>                  <p class="home-view-more"><a class="png24 home-more js-more" href="javascript:;"></a></p>                  <%}%>',
		homeYiren: '<li>                     <a <%if(live==1){%>href="/index.php/Show/index/roomnum/<%=room_id%>"<%}else{%>href="http://www.bobo.com/special/artists/"<%}%> target="_blank" data-keyfrom="taiwan">                        <img src="<%=img_url%>" /><%if(live==1){%><em class="yiren-time">\u76f4\u64ad</em><%}%><em class="png24 home-yiren-corner"></em>                     <span class="home-yiren-info"><em><%=nick%></em><%=title%></span>                     </a>                  </li>',
		homeZhubo: '<li class="js-zhubo-slider-item">                    <a href="/<%=room_id%>" target="_blank" data-keyfrom="zhuboGold">                         <div class="home-zhubo-modal png24"></div>                         <img src="<%=img_url%>" /><%if(live==1){%><em class="png24 live-tip">\u76f4\u64ad</em><%}%>                        <span class="home-zhubo-info"><%=Util.decodeSpecialHtmlChar(nick)%></span>                     </a>                  </li>',
		homeNews: '<div class="home-kb-t kb-item1"><a class="home-kb-text kb-ani" href="<%=data[1].link%>" target="_blank"><%=data[1].title%></a></div>                   <div class="home-kb-item kb-item0  kb-ani clearfix">                         <a class="fl" href="<%=data[0].link%>" target="_blank" data-keyfrom="kuaibao.pic"><img class="kb-fl" src="<%=Util.getImageUrl(data[0].img_url, 114, 100)%>" /></a>                        <div class="kb-item-text"><a href="<%=data[0].link%>" target="_blank" data-keyfrom="kuaibao.picword"><%=data[0].title%></a></div>                    </div>                  <%for (var i=2;i<data.length;i++){%>                  <div class="kb-ani kb-item<%=i%>"><a href="<%=data[i].link%>" target="_blank" data-keyfrom="kuaibao.word"><%=data[i].title%></a></div>                   <%}%>                  <a target="_blank" class="more kb-ani" href="http://www.bobo.com/special/bulletin/#action_all">+\u66f4\u591a</a>',
		homeSlide: '<div class="js-bgItem home-slide-bgItem" style="background: <%=color%>;">                     <div class="home-slide-wrapper">                        <div class="home-slide-item">                           <img src="<%=img_url%>" />                           <div class="home-slide-top3">                              <h2 style="color: <%=collection.c_color%>"><%=collection.c_name%></h2>                              <ul>                                 <%var list = collection.list%>                                 <%if (list){%>                                 <%for(var i=0;i<list.length;i++){%>                                 <li><em style="color: <%=collection.item_color%>" class="home-slide-num"><%=i+1%></em><a style="color: <%=collection.item_color%>"><span><%=Util.cutString(list[i].nick,0,6)%></span><%if(activity_id=="344"){%><%=Math.floor(list[i].score/10)%><%}else if(activity_id=="316"){%><%=Math.floor(list[i].score/100)%><%}else{%><%=list[i].score%><%}%></a></li>                                 <%}%>                                 <%}%>                              </ul>                           </div>                           <a class="home-slide-link" href="<%=link%>" data-keyfrom="top" target="_blank"></a>                        </div>                     </div>                  </div>',
		getBobi: '<li <%if(index%2!=0){%>class="odd"<%}%>>                  <div <%if(img!=11){%>class="js-hover"<%}%>>                     <%if(img!=11){%>                        <em class="fresh-icon png24 fresh-icon<%=index+1%>"></em>                     <%}else{%>                        <%if(img==11){%><a href="/special/android/?f=bobohome" target="_blank">                        <em class="fresh-icon png24 fresh-icon<%=img%>"></em>                        </a>                        <%}%>                     <%}%>                     <%if(img==11){%><a href="/special/android/?f=bobohome" target="_blank">                        <p><%=title%><br/><span>\u5956\u52b1\uff1a<%=cCurrency%>\u6ce2\u5e01</span></p>                     </a>                     <%}else{%>                        <p><%=title%><br/><span>\u5956\u52b1\uff1a<%=cCurrency%>\u6ce2\u5e01</span></p>                     <%}%>                  </div>                  <%if(receiveStatus==0){%>                     <%if(key!="REGISTER"){%>                     <span class="png24 fresh-info">\u5b8c\u6210\u5ea6: <%=quota%>/<%=total%></span>                     <%}else{%>                     <a class="png24 btn-register js-need-reg" href="javascript;"></a>                     <%}%>                  <%}else if(receiveStatus==1){%>                  <a class="png24 btn-receive js-receive" href="javascript:;" data-type="<%=key%>"></a>                  <span class="png24 fresh-received js-received" style="display: none;"></span>                  <%}else if(receiveStatus==2){%>                  <span class="png24 fresh-received"></span>                  <%}%>                  <div class="js-detail fresh-detail fresh-detail<%=index+1%>"><em class="png24 fresh-arrow"></em><img src="http://img1.cache.netease.com/bobo/image/task3/<%=img%>.jpg"></div>               </li>',
		imageWall: '<div class="global-photo-album clearfix js-photoAlbum">                     <a class="global-photo1" href="#"><img src="/image/avatar.jpg"></a>                     <div class="global-photo-inner clearfix">                        <a href="#"><img src="/image/avatar.jpg"></a>                        <a href="#"><img src="/image/avatar.jpg"></a>                        <a href="#"><img src="/image/avatar.jpg"></a>                        <a href="#"><img src="/image/avatar.jpg"></a>                        <a href="#"><img src="/image/avatar.jpg"></a>                        <a href="#"><img src="/image/avatar.jpg"></a>                        <a href="#"><img src="/image/avatar.jpg"></a>                        <a class="global-photo-last" href="#"><img src="/image/avatar.jpg"></a>                     </div>                     <a class="png24 home-shouqi" href="javascript:;"></a>                   </div>',
		familyRecommend: '<li data-value="<%=room_id%>" <%if(num%2==1){%> class="left-block"<%}%>>            <a class="family-recommend-avatar" href="/<%=room_id%>" target="_blank" title="<%=family_name%>" data-keyfrom="familyroom">            <%if(live_id>0){%><em class="png24 live-tip">\u76f4\u64ad</em><%}%>               <img src="http://imgsize.ph.126.net/?imgurl=<%=family_avatar%>_108x108x1x85.jpg" width="108" height="108">               <%if(live_id>0){%>               <div class="family-recommend-zhubo">               <img class="family-recommend-note" src="http://img1.cache.netease.com/bobo/image/avatar-note.gif"/>               <img class="family-recommend-zhubo-avatar" src="http://imgsize.ph.126.net/?imgurl=<%=avatar%>_40x40x1x85.jpg" alt="<%=nick%>" width="40" height="40"/>               </div>               <%}%>            </a>            <div class="family-recommend-info">               <a class="family-recommend-topic" href="/<%=room_id%>" target="_blank" data-keyfrom="familyroom"><%=title%></a>               <a class="family-recommend-nick family-recommend-nick-<%=num%>" href="/family/<%=family_id%>" target="_blank" data-keyfrom="family"><%=family_name%></a>               <%if(live_id > 0){%>               <p class="family-recommend-time">\u5df2\u5f00\u64ad\uff1a<%=time%></p>               <%}else {%>               <p class="family-recommend-time">\u5f00\u64ad\u65f6\u95f4\uff1a<%=time%></p>               <%}%>            </div>            <%if(live_id>0){%>            <a class="home-family-room family-recommend-icon png24" href="/<%=room_id%>" target="_blank" data-keyfrom="familyroom">\u53bb\u5bb6\u65cf\u623f</a>            <%}else {%>               <%if(follow){%>               <a class="js-family-unfollow home-family-room family-recommend-icon png24" href="javascript:;" data-roomId="<%=room_id%>">\u53d6\u6d88\u5173\u6ce8</a>               <a class="js-family-follow home-family-focus family-recommend-icon png24 hidden" href="javascript:;" data-roomId="<%=room_id%>">\u52a0\u5173\u6ce8</a>               <%} else {%>               <a class="js-family-unfollow home-family-room family-recommend-icon png24 hidden" href="javascript:;" data-roomId="<%=room_id%>">\u53d6\u6d88\u5173\u6ce8</a>               <a class="js-family-follow home-family-focus family-recommend-icon png24" href="javascript:;" data-roomId="<%=room_id%>">\u52a0\u5173\u6ce8</a>               <%}%>            <%}%>         </li>',
		voiceTpl: '<div class="home-voice-item">         <a href="http://chvoice.bobo.com/<%=roomId%>" target="_blank">            <img src="<%=Util.getImageUrl(liveCoverUrl,175,117)%>" width="175" height="117">            <%if(live > 0){%><em class="png24 home-voice-live"></em><%}%>            <p class="hot-anchor-cover"></p>            <p class="hot-anchor-fans"><em class="png24 icon-fansS"></em><%=onlineUserCount%></p>         </a>         <div>            <h5><a href="http://chvoice.bobo.com/<%=roomId%>" target="_blank"><%=anchorNick%></a></h5>            <p>\uff08\u623f\u95f4\u53f7<%=roomId%>\uff09</p>         </div>      </div>'
	};
	return i
});
define("message/Message", function(e, t, n) {
	var i = e("basic/jquery");
	var r = e("basic/Util");
	var s = e("common/Win");
	var a = e("common/Validate");
	var o = e("common/NaviTip");
	var l = e("message/Tpl");

	function c(e, t, n, a) {
		var o = this;
		this.userId = e;
		this.name = t;
		this.func = n || function(e) {};
		if (typeof a != "undefined") {
			this.isShade = a
		} else {
			this.isShade = true
		}
		i(r.formatTemplate(l.message, {
			name: r.encodeSpecialHtmlChar(o.name)
		})).appendTo("body");
		this.win = new s(i("#messageBox"), o.isShade, "display", true);
		this.init()
	}
	c.prototype.init = function() {
		var e = i("#messageInfo"),
			t = i("#mesError"),
			n = i("#mesCountWraper"),
			s = i("#mesErrorCountWraper"),
			l = i("#mesCount"),
			c = i("#mesErrorCount");
		var u = this;
		var f = new a.Validate("#messageForm", function() {
			i.post("/message/send.do", {
				toUserId: u.userId,
				message: e.val()
			}, function(e) {
				if (e.status == 1) {
					u.hide();
					o.show("\u53d1\u9001\u6210\u529f");
					u.func(e)
				} else if (e.status == 2) {
					o.show("\u6d89\u53ca\u654f\u611f\u8bcd\u8bed\uff0c\u8bf7\u91cd\u65b0\u7f16\u8f91", "error")
				} else if (e.status == 1002) {
					o.show("\u4eb2\uff0c\u5145\u503c10\u5757\u94b1\uff0c\u624d\u53ef\u4ee5\u7ed9Ta\u53d1\u79c1\u4fe1\u54df", "error")
				} else {
					o.show("\u53d1\u9001\u5931\u8d25", "error")
				}
			});
			return false
		});
		var d = new a.ValidateItem({
			node: e,
			check: function() {
				var e = i.trim(this.node.val());
				var a = r.getLength(e);
				if (e == "" || a > 163) {
					t.show();
					s.hide();
					n.hide();
					this.node.focus();
					return false
				} else {
					t.hide();
					return true
				}
			}
		});
		f.add(d);
		this.bindCheck(e, n, s, l, c, t);
		i("#messageSubmit").click(function() {
			i("#messageForm").submit()
		});
		setTimeout(function() {
			e.focus()
		}, 100)
	};
	c.prototype.bindCheck = function(e, t, n, s, a, o) {
		var l = this;

		function c() {
			var i = 163 - r.getLength(e.val());
			if (i >= 0) {
				t.show();
				n.hide();
				s.html(i)
			} else {
				t.hide();
				n.show();
				a.html(Math.abs(i))
			}
			o.hide()
		}
		e.bind("keyup", function(e) {
			if (e.ctrlKey && e.keyCode == 13) {
				e.preventDefault();
				i("#messageForm").submit();
				return false
			}
			if (e.keyCode == 17) {
				e.preventDefault();
				return false
			}
			if (l.countTimer) {
				clearTimeout(l.countTimer);
				l.countTimer = setTimeout(function() {
					c()
				}, 100)
			} else {
				l.countTimer = setTimeout(function() {
					c()
				}, 100)
			}
		})
	};
	c.prototype.show = function() {
		this.win.show()
	};
	c.prototype.hide = function() {
		this.win.hide()
	};
	return c
});
define("message/Reminder", function(e, t, n) {
	var i = e("basic/jquery");
	var r = e("basic/Util");
	var s = e("common/FloatBox/FloatBox");
	var a = e("message/Message");
	var o = e("message/Tpl");

	function l() {
		this.node = i("#msgNode");
		this.messageCount = i("#messageCount");
		this.init();
		this.bindEvent();
		this.cache = 1
	}
	l.prototype.getData = function() {
		var e = this;
		if (e.cache != 1) {
			e.render(e.cache)
		} else {
			i.post("/message/newMessagePop.do", function(t) {
				if (t.status == 1) {
					t.isLeader = CONFIG["isLeader"];
					t.cutString = r.cutString;
					t.Util = r;
					e.render(t);
					e.cache = t
				}
			});
			e.show()
		}
	};
	l.prototype.render = function(e) {
		this.mesReminder.html(r.formatTemplate(o.reminder, e))
	};
	l.prototype.show = function() {
		this.mesReminder.show()
	};
	l.prototype.hide = function() {
		this.mesReminder.hide()
	};
	l.prototype.init = function() {
		var e = this;
		this.mesReminder = i("#mesReminder");
		e.node.hover(function() {
			if (e.cache == 1) {
				e.getData()
			} else {
				e.show()
			}
		}, function() {
			e.hide()
		});
		this.mesReminder.mouseenter(function() {
			e.show()
		}).mouseleave(function() {
			e.hide()
		});
		e.setMessageCount()
	};
	l.prototype.setMessageCount = function() {
		var e = this;
		i.post("/message/newMessageCount.do", function(t) {
			if (t.status == 1) {
				var n = t.newMessageCount;
				if (n > 0) {
					if (n > 99) {
						n = 99
					}
					e.messageCount.show()
				} else {
					e.messageCount.hide()
				}
			}
		})
	};
	l.prototype.ignore = function(e) {
		var t = this;
		i.post("/message/resetSessionUnread.do", {
			withUserId: e
		}, function(e) {
			if (e.status == 1) {
				t.cache = 1;
				t.getData();
				setTimeout(function() {
					t.setMessageCount()
				}, 500)
			}
		})
	};
	l.prototype.bindEvent = function() {
		var e = this;
		i("body").delegate(".js-replymsg", "click", function() {
			var t = r.getLinkParam(this).toString(),
				n = this.getAttribute("username");
			new a(t, n, function() {
				e.ignore(t)
			}).show();
			return false
		});
		var t = new s;
		i("body").delegate(".js-boxer", "mouseover", function() {
			var n = this.getAttribute("userId");
			t.actionType = 2;
			t.action = function(t) {
				e.ignore(n)
			};
			t.hover(this, n)
		});
		i("body").delegate(".js-boxer", "mouseout", function() {
			var n = this.getAttribute("userId");
			t.actionType = 2;
			t.action = function(t) {
				e.ignore(n)
			};
			t.out(this, n)
		});
		i("body").delegate(".js-ignore", "click", function() {
			var t = r.getLinkParam(this).toString();
			e.ignore(t)
		});
		i("body").delegate(".js-newmsg", "mouseover", function() {
			i(this).addClass("hover")
		});
		i("body").delegate(".js-newmsg", "click", function(e) {
			var t = this.getAttribute("toUserId");
			var n = e.target || e.srcElement;
			if (!i(n).hasClass("js-replymsg") && !i(n).hasClass("js-ignore")) {
				window.location.href = "/message/talk?toUserId=" + t
			}
		});
		i("body").delegate(".js-newmsg", "mouseout", function() {
			i(this).removeClass("hover")
		})
	};
	return l
});
define("message/Tpl", function(e, t, n) {
	return {
		message: '<div class="dialogLayer msgdialogLayer" id="messageBox">                     <div class="dialogLayer-hd">                        <a class="png24 btn-close js-close" href="javascript:" title="\u5173\u95ed"></a>                     </div>                     <div class="dialogLayer-bd">   	                  <form id="messageForm">   	                   <h3>\u79c1\u4fe1 <strong><%=name%></strong></h3>   	                   <p><textarea id="messageInfo"></textarea></p>   	                   <p class="msg-send-info">   	                   <span class="fl" id="mesCountWraper">\u8fd8\u80fd\u8f93\u5165<em id="mesCount">163</em>\u5b57</span>   	                   <span class="fl" style="display:none" id="mesErrorCountWraper">\u5df2\u7ecf\u8d85\u51fa<em id="mesErrorCount" class="cDRed"></em>\u5b57</span>   	                   <span class="fl cDRed" id="mesError" style="display:none">\u79c1\u4fe1\u5185\u5bb9\u57281\u5230163\u5b57\u4e4b\u95f4</span>   	                   <a class="orange-btnS" href="javascript:" id="messageSubmit" title="\u53d1\u9001">\u53d1\u9001</a>   	                   </p>   	                  </form>                     </div>                  </div>',
		report: '<div class="dialogLayer repdialogLayer" id="reportBox">                  <div class="dialogLayer-hd">                     <a class="png24 btn-close js-close" href="javascript:;" title="\u5173\u95ed"></a>                  </div>                  <div class="dialogLayer-bd">   	               <h3>\u4f60\u8981\u4e3e\u62a5</h3>   	               <div class="report-msg-info">                        <a class="fl avatar-m"  href="javascript:;"  userId="<%=userId%>"  title="<%=name%>">                           <img src="<%=avatar%>"><em></em>                        </a>                        <em class="report-wealth png24 medal-wealth<%=wealthLevel%>"></em>   	                  <p><%=name%>                           <%if(isAnchor){%>                           <em class="report-anchor png24 medal-anchor<%=anchorLevel%>"></em>                           <%}%>                        </p>   	               </div>   	               <form id="messageForm">   	               <input type="hidden" value="" id="reportType"/>   	               <p class="report-msg-reason clearfix">   		               <label><input class="report-check js-reporttype" value="porn" type="checkbox">\u8272\u60c5\u4ea4\u6613</label>   		               <label><input class="report-check js-reporttype" value="ads" type="checkbox">\u5783\u573e\u5e7f\u544a</label>   		               <label><input class="report-check js-reporttype" value="abuse" type="checkbox">\u4eba\u8eab\u653b\u51fb\u6211</label>   		               <label><input class="report-check js-reporttype" value="sense" type="checkbox">\u654f\u611f\u4fe1\u606f</label>   		               <label><input class="report-check js-reporttype" value="fake" type="checkbox">\u865a\u5047\u4e2d\u5956\u4fe1\u606f</label>   		               <label><input class="report-check js-reporttype" value="other" type="checkbox">\u5176\u4ed6</label>   	               </p>   	               <p>   	                <textarea id="reportMessage"></textarea>   	                <span class="cDRed" id="reportError" style="display:none">\u8bf7\u9009\u62e9\u4e3e\u62a5\u7c7b\u578b</span>   	               </p>   	               </form>   	               <p>   	               <a class="orange-btnS" href="javascript:;"  id="reportSubmit"  title="\u63d0\u4ea4">\u63d0\u4ea4</a>   	               <a class="gray-btnS js-close" href="javascript:;" title="\u53d6\u6d88">\u53d6\u6d88</a>   	               </p>                  </div>               </div>',
		reminder: '<%if(msgCount==0){%>                 <div class="message-pop-view" style="display:block">				     <a href="/message">\u67e5\u770b\u79c1\u4fe1</a>				     <%if(admin || leader){%>				     <a  href="/message/team">\u67e5\u770b\u5bb6\u65cf\u6d88\u606f<%if(familyCount>0){%>\uff08<span class="cOrange"><%=familyCount%></span>\uff09<%}%></a>				     <%}%>				     <a href="/message/system">\u67e5\u770b\u7cfb\u7edf\u6d88\u606f<%if(sysCount>0){%>\uff08<span class="cOrange"><%=sysCount%></span>\uff09<%}%></a>			        </div>			        <%}else{%>			        <div class="message-pop" style="display:block">				        <div class="message-pop-wrapper">					         <div class="message-pop-list clearfix">		         	         <h4>\u65b0\u589e\u79c1\u4fe1\uff08<span class="cOrange"><%=msgCount%></span>\uff09</h4>		         	         <ul>		         		         <%for(var i=0;i<popMessageSessions.length;i++){%>		         		         <%if(i<2){%>		         		         <li class="js-newmsg" toUserId="<%=popMessageSessions[i].fromUserIdStr%>">		         			         <a class="fl avatar-m js-boxer" userId="<%=popMessageSessions[i].fromUserIdStr%>" href="#"><img src="<%=Util.getImageUrl(popMessageSessions[i].fromUserInfo.avatar,50)%>"><em></em></a>		         			         <h5><span title="<%=popMessageSessions[i].fromUserInfo.nick%>"><%=cutString(popMessageSessions[i].fromUserInfo.nick,0,8)%></span>		         			          <%if(popMessageSessions[i].fromUserInfo.anchor){%>		         			           <em  title="<%=Util.anchor[popMessageSessions[i].fromUserInfo.anchorLevel]%>" class="msg-pop-anchor png24 medal-anchor<%=popMessageSessions[i].fromUserInfo.anchorLevel%>"></em>		         			          <%}%>		         			           <em title="<%=Util.wealth[popMessageSessions[i].fromUserInfo.wealthLevel]%>" class="msg-pop-wealth png24 medal-wealth<%=popMessageSessions[i].fromUserInfo.wealthLevel%>"></em>		         			          </h5>		         			         <span class="message-pop-time"><%=popMessageSessions[i].timePretty%></span>		         			         <span class="msg-reply"><a class="png24 btn-msg-reply js-replymsg" title="\u56de\u590d" href="javascript://<%=popMessageSessions[i].fromUserIdStr%>" username="<%=popMessageSessions[i].fromUserInfo.nick%>"></a><a class="btn-msg-del js-ignore" title="\u5ffd\u7565" href="javascript://<%=popMessageSessions[i].fromUserIdStr%>"></a></span>		         			         <p><%=popMessageSessions[i].messageMap.msg%></p>		         			          <%if(i==1){%>		         			             <p class="msg-pop-control"><%var sb=msgCount-2; if(sb>0){%>\u8fd8\u6709<%=sb%>\u6761<%}%><a class="cOrange" href="/message">\u67e5\u770b\u5168\u90e8&gt;&gt;</a></p>		         			          <%}%>		         		         </li>		   		         		 <%}%>		         		         <%}%>		         	         </ul>		                  </div>	                  </div>	                  <%if(admin || leader){%>				         <div class="message-pop-wrapper">					         <a class="message-pop-btn" href="/message/team">\u67e5\u770b\u5bb6\u65cf\u6d88\u606f <%if(familyCount>0){%>\uff08<span class="cOrange"><%=familyCount%></span>\uff09<%}%></a>	                  </div>	                  <%}%>				         <div class="message-pop-wrapper">					         <a class="message-pop-btn" href="/message/system">\u67e5\u770b\u7cfb\u7edf\u6d88\u606f<%if(sysCount>0){%>\uff08<span class="cOrange"><%=sysCount%></span>\uff09<%}%></a>	                  </div>			       </div>			       <%}%>'
	}
});
define("shop/BuyBox", function(e, t, n) {
	"use strict";
	var i = e("basic/jquery");
	var r = e("basic/oop/Class");
	var s = e("common/FloatWin/FloatWin");
	var a = e("basic/Util");
	var o = e("shop/tpl");
	var l = e("shop/SearchAccount");
	var c = e("common/NaviTip");
	var u = e("shop/BuySuccessBox");
	var f = e("basic/Observer");
	var d = e("common/Log/Logger");
	var p = r.extend({
		init: function(e, t) {
			this.$elem = null;
			this.data = i.extend({
				balance: e.balance
			}, e.item) || {};
			this.config = t || {};
			this.packageSave = e.item.packageSave;
			this.type = this.packageSave ? "year" : "month";
			this.payMethod = t.payMethod
		},
		preRender: function() {
			if (this.data.features != null) {
				this.data.shortFeatures = i.map(this.data.features, function(e, t) {
					return a.countChars(e, 16)
				})
			}
			this.data.image = a.getImageUrl(this.data.image, 124, 80);
			this.formatMoney();
			var e = i.extend({
				config: this.config
			}, this.data);
			return {
				content: a.formatTemplate(o.buyBox, e)
			}
		},
		postRender: function(e) {
			e.addClass("mallbuyLayer");
			this.$elem = e
		},
		formatMoney: function() {
			this.data.formatBalance = a.formatNumber(this.data.balance);
			this.data.formatPackageSave = a.formatNumber(this.data.packageSave);
			this.data.formatPriceMonth = a.formatNumber(this.data.priceMonth);
			this.data.formatPriceYear = a.formatNumber(this.data.priceYear)
		},
		show: function() {
			this.buyBox = s.PopWin({
				preRender: i.proxy(this.preRender, this),
				postRender: i.proxy(this.postRender, this)
			});
			this.buyBox.show();
			this.bindEvent();
			this.onMoney()
		},
		bindEvent: function() {
			this.$elem.delegate(".js-account-btn", "click", i.proxy(this.onSwitchAccount, this)).delegate(".js-buy-type", "click", i.proxy(this.onBuyType, this)).delegate(".js-time-input", "click", i.proxy(this.onFocusTimeInput, this)).delegate(".js-time-select a", "click", i.proxy(this.onTimeSelect, this)).delegate(".js-submit", "click", i.proxy(this.onSubmit, this)).delegate(".js-account-input", "focus", i.proxy(this.onFocusAccountInput, this));
			this.$elem.bind("click", function(e) {
				if (!i(e.target).hasClass("js-time-input")) i(this).find(".js-time-select").hide()
			});
			new l(this.$elem.find(".js-account"))
		},
		onSwitchAccount: function(e) {
			var t = i(e.currentTarget);
			t.parents(".js-account").hide().siblings(".js-account").show();
			this.$elem.find(".js-account-input").val(this.$elem.find(".js-account-input").attr("data-defaultTip"));
			this.payMethod = t.attr("data-method");
			this.onFocusAccountInput()
		},
		onBuyType: function(e) {
			var t = i(e.currentTarget);
			var n = t.attr("data-type");
			var r = t.attr("data-num");
			this.$elem.find(".js-buy-type").removeClass("cur");
			this.$elem.find(".js-buy-" + n).addClass("cur");
			this.$elem.find(".js-time").hide();
			this.$elem.find(".js-money").hide();
			this.$elem.find(".js-" + n).show();
			this.$elem.find(".js-money-" + n).show();
			this.$elem.find(".js-" + n + "-input").val(r);
			this.type = n;
			this.onMoney()
		},
		onFocusTimeInput: function(e) {
			var t = i(e.currentTarget);
			t.siblings(".js-time-select").show()
		},
		onTimeSelect: function(e) {
			var t = i(e.currentTarget);
			t.parents(".js-time-select").siblings(".js-time-input").val(t.text());
			this.onMoney()
		},
		onMoney: function() {
			var e = parseInt(this.$elem.find(".js-" + this.type + "-input").val(), 10);
			var t = this.type == "year" ? 12 : 1;
			var n = parseInt(this.data.priceMonth, 10);
			var i = this.type == "year" && this.packageSave ? parseInt(this.data.packageSave, 10) : 0;
			if (this.type == "month" && e == 12) {
				t = 12;
				e = 1;
				i = parseInt(this.data.packageSave, 10)
			}
			this.orginalMoney = e * t * n;
			this.totalMoney = e * t * n - i * e;
			this.$elem.find(".js-originalMoney").text(a.formatNumber(this.orginalMoney));
			this.$elem.find(".js-totalMoney").text(a.formatNumber(this.totalMoney));
			this.onLackMoney()
		},
		onLackMoney: function() {
			if (this.data.balance < this.totalMoney) {
				this.$elem.find(".js-lackMoney-tip").show();
				this.$elem.find(".js-cash-error").hide();
				this.$elem.find(".js-lackMoney").text(a.formatNumber(this.totalMoney - this.data.balance));
				this.$elem.find(".js-pay-btn").addClass("mallpay-disabled").removeClass("js-submit")
			} else {
				this.$elem.find(".js-lackMoney-tip").hide();
				this.$elem.find(".js-pay-btn").removeClass("mallpay-disabled").addClass("js-submit")
			}
		},
		checkInput: function() {
			if (!this.getNick()) {
				this.$elem.find(".js-account-error").show().find(".js-error-info").text("\u8bf7\u8f93\u5165\u6635\u79f0\uff01");
				return false
			}
			return true
		},
		getNick: function() {
			var e = this.$elem.find(".js-account-input").val();
			if (this.payMethod == "give" && e != "" && e != this.$elem.find(".js-account-input").attr("data-defaultTip")) {
				return encodeURIComponent(e)
			}
			return ""
		},
		onSubmit: function() {
			if (i.isPlainObject(this.Request) && this.Request.readyState !== 4) {
				return
			}
			if (this.payMethod == "give" && !this.checkInput()) return;
			var e = {
				itemId: this.data.itemId,
				quantity: this.$elem.find(".js-" + this.type + "-input").val(),
				unit: this.type == "year" ? "y" : "m"
			};
			this.getNick() && (e.nick = this.getNick());
			var t = this;
			this.Request = i.ajax({
				type: "POST",
				url: "/shop/buy.do",
				data: e,
				dataType: "json",
				success: function(e) {
					if (e.status == 1) {
						var n = false;
						if (e["boquan"]) {
							n = true
						}
						var r = i.extend({}, e.result, {
							payMethod: t.payMethod,
							isBojuan: n
						});
						new u(r).show();
						d.triggerLog(t.$elem, {
							method: "buysuccess"
						})
					} else {
						t.showError(e.status)
					}
				},
				error: function() {
					c.show({
						type: "error",
						text: "\u7f51\u7edc\u8bf7\u6c42\u51fa\u9519\u5566\uff0c\u8bf7\u5237\u65b0\u91cd\u8bd5\uff01"
					})
				}
			})
		},
		showError: function(e) {
			this.$elem.find(".js-error").hide();
			if (e == 509) {
				i(document).trigger("error", ["needLogin"])
			} else if (e == 506) {
				this.$elem.find(".js-account-error").show().find(".js-error-info").text("\u6ca1\u6709\u627e\u5230\u8be5\u6635\u79f0\u7684\u7528\u6237\uff01")
			} else if (e == 603) {
				this.$elem.find(".js-account-error").show().find(".js-error-info").text("\u6b64\u8d26\u53f7\u9700\u52a0\u5165\u5bb6\u65cf\u624d\u80fd\u8d2d\u4e70\u6216\u63a5\u6536\u9053\u5177\uff01")
			} else if (e == 602) {
				this.$elem.find(".js-cash-error").show().find(".js-error-info").text("\u4e13\u5c5e\u9053\u5177\u4e0d\u80fd\u8d2d\u4e70\uff01")
			} else if (e == 614) {
				this.$elem.find(".js-cash-error").show().find(".js-error-info").text("\u8d22\u5bcc\u7b49\u7ea7\u4e0d\u591f\uff01")
			} else if (e == 615) {
				this.$elem.find(".js-cash-error").show().find(".js-error-info").text("\u5546\u54c1\u6570\u91cf\u4e0d\u8db3\uff01")
			} else if (e == 616) {
				this.$elem.find(".js-cash-error").show().find(".js-error-info").text("\u6d3b\u52a8\u5df2\u7ed3\u675f\uff0c\u5ea7\u9a7e\u5df2\u5356\u5b8c\uff01")
			} else if (e == 0) {
				this.$elem.find(".js-cash-error").show().find(".js-error-info").text("\u8d2d\u4e70\u5931\u8d25\uff01")
			} else if (e == 618) {
				this.$elem.find(".js-cash-error").show().find(".js-error-info").text("\u4f1a\u5458\u7279\u4ef7\u793c\u7269\u4e0d\u80fd\u8d60\u9001\uff01")
			}
		},
		onFocusAccountInput: function() {
			this.$elem.find(".js-account-error").hide()
		}
	});
	return p
});
define("shop/BuySpecialRoomBox", function(e, t, n) {
	"use strict";
	var i = e("basic/jquery");
	var r = e("basic/oop/Class");
	var s = e("common/FloatWin/FloatWin");
	var a = e("shop/BuySpecialRoomSuccessBox");
	var o = e("basic/Util");
	var l = e("shop/tpl");
	var c = e("common/Log/Logger");
	var u = r.extend({
		init: function(e, t) {
			this.$elem = null;
			this.data = i.extend({
				balance: e.balance
			}, e.item) || {};
			this.config = t || {};
			this.data.image = CONFIG["avatar"];
			this.type = "month";
			this.payMethod = t.payMethod
		},
		preRender: function() {
			if (this.data.features != null) {
				this.data.shortFeatures = i.map(this.data.features, function(e, t) {
					return o.countChars(e, 16)
				})
			}
			this.data.image = "http://img1.cache.netease.com/bobo/image/page/shop/specialroom/specialroom_logo.png";
			this.formatMoney();
			var e = i.extend({
				config: this.config
			}, this.data);
			return {
				content: o.formatTemplate(l.buySepcialRoomBox, e)
			}
		},
		postRender: function(e) {
			e.addClass("mallbuyLayer");
			this.$elem = e
		},
		formatMoney: function() {
			this.data.formatBalance = o.formatNumber(this.data.balance);
			this.data.formatPackageSave = o.formatNumber(this.data.packageSave);
			this.data.formatPriceMonth = o.formatNumber(this.data.priceMonth);
			this.data.formatPriceYear = o.formatNumber(this.data.priceYear)
		},
		show: function() {
			this.buyBox = s.PopWin({
				preRender: i.proxy(this.preRender, this),
				postRender: i.proxy(this.postRender, this)
			});
			this.buyBox.show();
			this.bindEvent();
			this.onMoney()
		},
		bindEvent: function() {
			this.$elem.delegate(".js-submit", "click", i.proxy(this.onSubmit, this))
		},
		onMoney: function() {
			this.totalMoney = parseInt(this.data.priceMonth, 10);
			this.$elem.find(".js-totalMoney").text(o.formatNumber(this.totalMoney));
			this.onLackMoney()
		},
		onLackMoney: function() {
			if (this.data.balance < this.totalMoney) {
				this.$elem.find(".js-lackMoney-tip").show();
				this.$elem.find(".js-cash-error").hide();
				this.$elem.find(".js-lackMoney").text(o.formatNumber(this.totalMoney - this.data.balance));
				this.$elem.find(".js-pay-btn").addClass("mallpay-disabled").removeClass("js-submit")
			} else {
				this.$elem.find(".js-lackMoney-tip").hide();
				this.$elem.find(".js-pay-btn").removeClass("mallpay-disabled").addClass("js-submit")
			}
		},
		onSubmit: function() {
			if (i.isPlainObject(this.Request) && this.Request.readyState !== 4) {
				return
			}
			var e = {
				itemId: this.data.itemId,
				quantity: this.$elem.find(".js-" + this.type + "-input").val(),
				unit: "m"
			};
			var t = this;
			var n = this.config.sepcialroomRenew ? "/shop/renew.do" : "/shop/buy.do";
			this.Request = i.ajax({
				type: "POST",
				url: n,
				data: e,
				dataType: "json",
				success: function(e) {
					if (e.status == 1) {
						var n = i.extend({}, e.result, {
							sepcialroomRenew: t.config.sepcialroomRenew
						});
						new a(n).show();
						c.triggerLog(t.$elem, {
							method: "buysuccess"
						})
					} else {
						t.showError(e)
					}
				},
				error: function() {
					NaviTip.show({
						type: "error",
						text: "\u7f51\u7edc\u8bf7\u6c42\u51fa\u9519\u5566\uff0c\u8bf7\u5237\u65b0\u91cd\u8bd5\uff01"
					})
				}
			})
		},
		showError: function(e) {
			this.$elem.find(".js-error").hide();
			if (e.status == 509) {
				i(document).trigger("error", ["needLogin"])
			} else if (e.status == 1013) {
				this.showErrorTips(e.errorMsg)
			}
		},
		showErrorTips: function(e) {
			this.$elem.find(".js-account-error").show().find(".js-error-info").text(e)
		}
	});
	return u
});
define("shop/BuySpecialRoomSuccessBox", function(e, t, n) {
	"use strict";
	var i = e("basic/jquery");
	var r = e("basic/oop/Class");
	var s = e("common/FloatWin/FloatWin");
	var a = e("basic/Util");
	var o = e("shop/tpl");
	var l = e("basic/Observer");
	var c = r.extend({
		defaults: {
			sepcialroomRenew: false
		},
		init: function(e) {
			this.data = i.extend({}, this.defaults, e);
			this.buySuccess = new l.Publisher("buy:success")
		},
		preRender: function() {
			return {
				content: a.formatTemplate(o.buySepcialRoomSuccessBox, this.data)
			}
		},
		postRender: function(e) {
			e.addClass("buySuccessLayer");
			this.$elem = e;
			this.bindEvent()
		},
		bindEvent: function() {
			this.$elem.find(".js-close").click(i.proxy(function() {
				this.buySuccess.deliver(a.formatNumber(this.data.cCurrency));
				a.refresh()
			}, this))
		},
		show: function() {
			s.PopWin({
				preRender: i.proxy(this.preRender, this),
				postRender: i.proxy(this.postRender, this)
			}).show()
		}
	});
	return c
});
define("shop/BuySuccessBox", function(e, t, n) {
	"use strict";
	var i = e("basic/jquery");
	var r = e("basic/oop/Class");
	var s = e("common/FloatWin/FloatWin");
	var a = e("basic/Util");
	var o = e("shop/tpl");
	var l = e("basic/Observer");
	var c = r.extend({
		init: function(e) {
			this.data = i.extend({}, e);
			this.buySuccess = new l.Publisher("buy:success")
		},
		preRender: function() {
			return {
				content: a.formatTemplate(o.buySuccessBox, this.data)
			}
		},
		postRender: function(e) {
			e.addClass("buySuccessLayer");
			this.$elem = e;
			this.bindEvent()
		},
		bindEvent: function() {
			this.$elem.find(".js-close").click(i.proxy(function() {
				this.buySuccess.deliver(a.formatNumber(this.data.cCurrency));
				a.refresh()
			}, this))
		},
		show: function() {
			s.PopWin({
				preRender: i.proxy(this.preRender, this),
				postRender: i.proxy(this.postRender, this)
			}).show()
		}
	});
	return c
});
define("shop/SearchAccount", function(e, t, n) {
	var i = e("basic/jquery");
	var r = e("basic/oop/Class");
	var s = e("shop/SearchList");
	var a = e("basic/Util");
	var o = e("shop/tpl");
	var l = r.extend({
		init: function(e) {
			this.$elem = i(e);
			this.$input = this.$elem.find(".js-input");
			this.defaultTip = this.$input.attr("data-defaultTip");
			this.searchListWrap = this.$elem.find(".js-wrap").hide();
			this.searchList = new s(this.$elem.find(".js-list"));
			this.cache = {};
			this.property = ["byNick"];
			this.bindEvent()
		},
		bindEvent: function() {
			this.$input.bind("keyup", i.proxy(this.onInput, this)).bind("focus", i.proxy(this.onFocus, this)).bind("blur", i.proxy(this.onBlur, this));
			this.searchListWrap.bind("select.SearchSuggestList", i.proxy(this.onSelect, this))
		},
		onInput: function(e) {
			if (e.which === 38 || e.which === 40 || e.which === 13) {
				return
			}
			this.intervalTime && clearTimeout(this.intervalTime);
			var t = this.$input.val();
			var n = a.countChars(a.encodeSpecialHtmlChar(t), 16);
			if (this.cache[t] !== undefined) {
				this.onCallBack(this.cache[t])
			} else {
				if (!i.trim(t).length || i.trim(t) === this.defaultTip) {
					this.hideSuggest();
					return
				}
				this.onLoad();
				var r = this;
				this.intervalTime = setTimeout(function() {
					i.ajax({
						type: "POST",
						url: "/searchUser",
						data: {
							word: t
						},
						dataType: "json",
						success: function(e) {
							e.input = n;
							r.cache[t] = e;
							if (t == r.$input.val()) {
								r.onCallBack(e)
							}
						}
					})
				})
			}
		},
		onLoad: function() {},
		onCallBack: function(e) {
			if (!this.onHaveProperty(e)) return;
			this.searchListWrap.show();
			var t = a.formatTemplate(o.suggestList, e);
			this.searchList.$elem.html(t);
			this.searchList.bindEvent()
		},
		onHaveProperty: function(e) {
			var t = false;
			for (var n = 0; n < this.property.length; n++) {
				if (e[this.property].length) t = true
			}
			return t
		},
		onSelect: function(e, t) {
			this.$input.val(t.value);
			this.hideSuggest()
		},
		onFocus: function(e) {
			this.$input.removeClass("cGray");
			if (i.trim(this.$input.val()) === this.defaultTip) {
				this.$input.val("")
			}
			this.onInput(e)
		},
		onBlur: function(e) {
			this.$input.addClass("cGray");
			if (i.trim(this.$input.val()) === "") {
				this.$input.val(this.defaultTip)
			}
			i(document).bind("click", i.proxy(this.autoClose, this))
		},
		autoClose: function(e) {
			var t = i(e.target).parents().andSelf();
			if (i.inArray(this.$elem[0], t) === -1) {
				this.hideSuggest()
			}
		},
		hideSuggest: function() {
			this.hideTimer && clearTimeout(this.hideTimer);
			var e = this;
			this.hideTimer = setTimeout(function() {
				e.searchList && e.searchList.unbindEvent();
				e.searchListWrap.hide()
			}, 100);
			i(document).unbind("click", i.proxy(this.autoClose, this))
		}
	});
	return l
});
define("shop/SearchList", function(e, t, n) {
	var i = e("basic/jquery");
	var r = e("common/SuggestList");
	var s = r.extend({
		onEnter: function(e) {
			this.onClick(e)
		},
		onClick: function(e) {
			var t = this.$elem.find(".current").data();
			this.$elem.trigger("select.SearchSuggestList", t)
		},
		show: function(e, t) {
			if (!this._super(e.html())) {
				return
			}
			if (!t) {
				this.$elem.html(e).children(":first").addClass("current")
			}
			this.$elem.html(e);
			this.cacheHtml = e;
			this.bindEvent();
			return
		},
		clearUp: function() {
			this.$elem.empty()
		},
		onKey: function(e) {
			this._super(e);
			if (e.which === 27) {
				this.$elem.parent().hide();
				e.preventDefault()
			}
		}
	});
	return s
});
define("shop/tpl", function(e, t, n) {
	var i = {
		buyBox: '<div class="mall-buy-product">                  <div class="mall-buy-pic"><img src="<%=image%>" /><b></b></div>                  <h4><%=name%></h4>                  <h5><em class="png24 icon-bobi" title="\u6ce2\u5e01"></em><strong><%=formatPriceMonth%></strong>/\u6708</h5>                  <ul>                     <li>\u6309\u6708\u8d2d\u4e70\uff1a30\u5929/\u6708</li>                     <li>\u6309\u5e74\u8d2d\u4e70\uff1a365\u5929/\u5e74</li>                     <li class="has-border">\u5546\u54c1\u6709\u6548\u671f\u5185\u8d2d\u4e70\u672c\u5546\u54c1\uff0c\u6709\u6548\u671f\u7d2f\u52a0\u3002</li>                     <li>\u5176\u4ed6\u60c5\u51b5\uff0c\u6709\u6548\u671f\u4ece\u8d2d\u4e70\u65f6\u7b97\u8d77</li>                  </ul>               </div>               <div class="mallpay-method">                  <div class="mallpay-method-hd get-account-hd js-account" <%if(config.payMethod!="buy"){%>style="display: none;"<%}%>>                     <p><span class="fl">\u5f00\u901a\u8d26\u53f7\uff1a<strong><%=CONFIG["nick"]%></strong></span><a class="fr cBlue js-account-btn" href="javascript:;" data-method="give">\u8d60\u9001\u4ed6\u4eba</a></p>                     <span class="get-account-error js-account-error js-error" style="display: none;"><em class="png24 icon-error"></em><span class="js-error-info"></span></span>                  </div>                  <div class="mallpay-method-hd get-account-hd get-account-cur js-account" <%if(config.payMethod!="give"){%>style="display: none;"<%}%>>                     <p><span class="fl">\u8d60\u9001\u6635\u79f0\uff1a<input class="get-account cGray js-account-input js-input" type="text" data-defaultTip="\u8bf7\u8f93\u5165\u6635\u79f0" value="\u8bf7\u8f93\u5165\u6635\u79f0"></span><a class="cBlue js-account-btn" href="javascript:;" data-method="buy">\u4e70\u7ed9\u81ea\u5df1</a></p>                     <span class="get-account-error js-account-error js-error" style="display: none;"><em class="png24 icon-error"></em><span class="js-error-info"></span></span>                     <div class="get-account-list js-wrap" style="display: none;">                        <div class="getaccount-result js-list">                        </div>                     </div>                  </div>                  <div class="mallpay-method-bd">                     <p class="mallpay-way">                     \u8d2d\u4e70\u65b9\u5f0f\uff1a<a class="js-buy-type js-buy-month <%if(!packageSave){%>cur<%}%>" data-type="month" data-num="3" href="javascript:;" title="\u6309\u6708\u4ed8\u8d39">\u6309\u6708\u4ed8\u8d39</a><a class="js-buy-type js-buy-year <%if(packageSave){%>cur<%}%>" data-type="year" data-num="1" href="javascript:;" title="\u6309\u5e74\u4ed8\u8d39">\u6309\u5e74\u4ed8\u8d39</a><%if(packageSave){%><em>\u4f18\u60e0</em><%}%>                     </p>                     <div class="mallpay-time js-time js-month"<%if(packageSave){%> style="display: none;"<%}%>>                        \u8d2d\u4e70\u65f6\u957f\uff1a<input class="mallpay-time-input js-time-input js-month-input" type="text" data-unit="m" value="3" readonly/>\u4e2a\u6708                        <%if(packageSave){%><span class="mallpay-change">\u9009\u62e9\u201c<a href="javascript:;" class="js-buy-type" data-type="year" data-num="1">\u6309\u5e74\u4ed8\u8d39</a>\u201d\u66f4\u4f18\u60e0</span><%}%>                        <p class="mallpay-time-select js-time-select" style="display: none;">                           <a href="javascript:;" title="1">1</a><a href="javascript:;" title="2">2</a>                           <a href="javascript:;" title="3">3</a><a href="javascript:;" title="4">4</a>                           <a href="javascript:;" title="5">5</a><a href="javascript:;" title="6">6</a>                           <a href="javascript:;" title="7">7</a><a href="javascript:;" title="8">8</a><a href="javascript:;" title="9">9</a>                           <a href="javascript:;" title="10">10</a><a href="javascript:;" title="11">11</a><a href="javascript:;" title="12">12</a>                        </p>                     </div>                     <div class="mallpay-time js-time js-year"<%if(!packageSave){%> style="display: none;"<%}%>>                        \u8d2d\u4e70\u65f6\u957f\uff1a<input class="mallpay-time-input js-time-input js-year-input" type="text" data-unit="y" value="1" readonly>\u5e74                        <p class="mallpay-time-select mallpay-year-select js-time-select" style="display: none;">                            <a href="javascript:;" title="1">1</a><a href="javascript:;" title="2">2</a>                        </p>                     </div>                     <div class="mallpay-account">                        <% if(!packageSave){ %>                        <p>\u5e94\u4ed8\u91d1\u989d\uff1a<strong class="js-totalMoney"></strong>\u6ce2\u5e01 <span class="mallpay-account-total">\u8d26\u6237\u4f59\u989d<em><%=formatBalance%></em>\u6ce2\u5e01</span></p>                        <% }else{ %>                        <p class="js-money js-money-month" style="display: none;">\u5e94\u4ed8\u91d1\u989d\uff1a<strong class="js-totalMoney"></strong>\u6ce2\u5e01 <span class="mallpay-account-total">\u8d26\u6237\u4f59\u989d<em><%=formatBalance%></em>\u6ce2\u5e01</span></p>                        <div class="js-money js-money-year">                           <p>\u5e94\u4ed8\u91d1\u989d\uff1a<span class="mallpay-account-original">\u539f\u4ef7 <em class="js-originalMoney"></em>\u6ce2\u5e01</span></p>                           <p class="mallpay-account-pay">\u4f18\u60e0\u4ef7<strong><b class="js-totalMoney"></b>\u6ce2\u5e01</strong> <span class="mallpay-account-total">\u8d26\u6237\u4f59\u989d<em><%=formatBalance%></em>\u6ce2\u5e01</span></p>                        </div>                        <% } %>                        <span class="mallpay-account-empty js-lackMoney-tip" style="display: none;">                           <em class="png24 icon-error"></em>\u4f59\u989d\u4e0d\u8db3\uff0c\u7f3a\u5c11<span class="js-lackMoney"></span>\u6ce2\u5e01<a href="http://www.bobo.com/pay" data-method="charge" target="_blank">\u53bb\u5145\u503c</a>                        </span>                        <span class="mallpay-account-empty js-cash-error js-error" style="display: none;">                           <em class="png24 icon-error"></em><span class="js-error-info"></span>                        </span>                     </div>                     <p class="mallpay-btn"><a class="orange-btnS js-pay-btn js-submit" href="javascript:;">\u8d2d\u4e70</a></p>                  </div>               </div>',
		buySepcialRoomBox: '<div class="mall-buy-product">                              <div class="mall-buy-pic mall-buy-sepcialroom-pic"><img src="<%=image%>" /><b></b></div>                              <h4 class="mall-buy-sepcialroom-title"><%= categoryName %></h4>                              <h5><em class="png24 icon-bobi" title="\u6ce2\u5e01"></em><strong><%=formatPriceMonth%></strong>\u6ce2\u5e01</h5>                              <p class="mall-buy-sepcialroom-text"><%= features.join("<br />") %></p>                           </div>                           <div class="mallpay-method">                              <div class="mallpay-method-hd get-account-hd js-account" <%if(config.payMethod!="buy"){%>style="display: none;"<%}%>>                                 <p><span class="fl">\u5f00\u901a\u8d26\u53f7\uff1a<strong><%=CONFIG["nick"]%></strong></span></p>                                 <span class="get-account-error js-account-error js-error" style="left:13px; display: none;"><em class="png24 icon-error"></em><span class="js-error-info"></span></span>                              </div>                              <div class="mallpay-method-bd">                                 <input class="js-time-input js-month-input" type="hidden" data-unit="m" value="1" readonly/>                                 <input class="js-time-input js-year-input" type="hidden" data-unit="y" value="1" readonly>                                 <ul class="mall-buy-sepcialroom-list">                                    <li>\u623f\u95f4\u53f7\u7801\uff1a<%= name %></li>                                    <li>\u623f\u95f4\u5730\u5740\uff1ahttp://www.bobo.com/<%= name %></li>                                    <!--<li>\u5e94\u4ed8\u91d1\u989d\uff1a<span class="js-totalMoney"></span> \u6ce2\u5e01</li>-->                                    <!--<li>\u8d26\u6237\u4f59\u989d\uff1a<%= formatBalance %> \u6ce2\u5e01</li>-->                                 </ul>                                 <div class="mallpay-account">                                    <p>\u5e94\u4ed8\u91d1\u989d\uff1a<strong class="js-totalMoney"></strong>\u6ce2\u5e01 <span class="mallpay-account-total">\u8d26\u6237\u4f59\u989d<em><%=formatBalance%></em>\u6ce2\u5e01</span></p>                                    <span class="mallpay-account-empty js-lackMoney-tip" style="display: none;">                                       <em class="png24 icon-error"></em>\u4f59\u989d\u4e0d\u8db3\uff0c\u7f3a\u5c11<span class="js-lackMoney"></span>\u6ce2\u5e01<a href="http://www.bobo.com/pay" data-method="charge" target="_blank">\u53bb\u5145\u503c</a>                                    </span>                                    <span class="mallpay-account-empty js-cash-error js-error" style="display: none;">                                       <em class="png24 icon-error"></em><span class="js-error-info"></span>                                    </span>                                 </div>                                 <p class="mallpay-btn"><a class="orange-btnS js-pay-btn js-submit" href="javascript:;">\u8d2d\u4e70</a></p>                              </div>                           </div>',
		suggestList: '<h4>\u6635\u79f0</h4>                     <ul>                        <% for(var i=0;i<byNick.length;i++){%>                        <li <%if(i==0){%>class="current"<%}%> data-value="<%=byNick[i].nick%>" data-userId="<%=byNick[i].userId%>">                           <a><img src="<%=byNick[i].avatar%>"></a>                              <h5><%=byNick[i].nick%></h5>                              <p>\u7c89\u4e1d\uff1a<%=byNick[i].followedCount%></p>                        </li>                        <%}%>                     </ul>',
		buySuccessBox: '<em class="png24 buy-suc"></em>                      <h2>\u8d2d\u4e70\u6210\u529f</h2>                      <p class="text"><%if(payMethod=="buy"){%>\u4f60<%}else if(payMethod=="give"){%>\u5bf9\u65b9<%}%>\u53ef\u4ee5\u5728 <%=formatExpireTime%> \u524d\u4f7f\u7528\u8be5\u5546\u54c1<%if(payMethod=="buy" && !equiped && category!="vip"){%>\u3002\u9a6c\u4e0a\u53bb <a class="cOrange" href="/user/tools?category=<%=category%>">\u6211\u7684\u9053\u5177</a> \u5f00\u542f\u4f7f\u7528<%}%><%if(isBojuan){%><br/>30000\u6ce2\u52b5\u5df2\u81ea\u52a8\u53d1\u9001\u5230\u4f60\u7684\u8d26\u6237<%}%></p>                      <p class="text buysuc-btn"><a class="gray-btn js-close" href="javascript:;">\u5173\u95ed</a><%if(payMethod=="buy" && CONFIG["pageName"]!="tools" && CONFIG["pageName"]!="myVip"){%><%if(category=="vip"){%><a class="orange-btn" href="/user/vip">\u6211\u7684\u4f1a\u5458</a><%}else{%><a class="orange-btn" href="/user/tools?category=<%=category%>">\u6211\u7684\u9053\u5177</a><%}%><%}%></p>',
		buySepcialRoomSuccessBox: '<em class="png24 buy-suc"></em>                      <h2>                        <p style="font-size: 18px;">                        <% if (sepcialroomRenew) { %>                           \u606d\u559c\u60a8\uff0c\u7eed\u8d39\u6210\u529f\uff01<br />\u623f\u95f4\u4f1a\u57281\u5c0f\u65f6\u5185\u4e3a\u60a8\u91cd\u65b0\u5f00\u901a\uff01                        <% } else { %>                           \u606d\u559c\u60a8\uff0c\u8363\u5347\u4e13\u5c5e\u623f\u623f\u4e3b\uff01<br />\u8bf7\u70b9\u51fb\u9875\u9762\u53f3\u4e0a\u89d2\u201c\u7533\u8bf7\u5165\u9a7b\u201d\uff0c\u901a\u8fc7\u5ba1\u6838\u540e\u5373\u53ef\u5f00\u901a\u623f\u95f4\uff01                        <% } %>                        </p>                      </h2>                      <p class="text buysuc-btn"><a class="gray-btn js-close" href="javascript:;">\u5173\u95ed</a></p>',
		carItem: '<%for(var i = 0; i < data.length; i++){%>                  <%if(data[i].shopType==2){%>                  <li class="shop-item" id="shopTimeSale">                  <%}else{%>                  <li class="shop-item">                  <%}%>                    <%if(data[i].shopType==1){%>                      <span class="png24 shop-tip">\u65b0\u54c1</span>                      <p class="shop-sale-info"></p>                    <%}else if(data[i].shopType==2){%>                       <span class="png24 shop-tip">\u9650\u65f6</span>                       <p class="shop-sale-info js-time-left" data-time="<%=data[i].paramMap.leftTime%>">\u5269<em class="js-hour">0</em>\u65f6                       <em class="js-min">0</em>\u5206<em class="js-second">0</em>\u79d2</p>                    <%}else if(data[i].shopType==3){%>                      <span class="png24 shop-tip">\u7279\u4ef7</span>                      <p class="shop-sale-info">\u539f\u4ef7  1\u6708\uff1a<em><%=data[i].priceMonth%></em>\u6ce2\u5e01</p>                    <%}else if(data[i].shopType==4){%>                      <span class="png24 shop-tip">\u9650\u91cf</span>                      <p class="shop-sale-info">\u672c\u6708\u5269<%=data[i].paramMap.leftCount%>/<%=data[i].paramMap.allCount%></p>                    <%}else if(data[i].shopType==5){%>                      <span class="png24 shop-tip">\u4f1a\u5458</span>                      <p class="shop-sale-info">\u539f\u4ef7  1\u6708\uff1a<em><%=data[i].priceMonth%></em>\u6ce2\u5e01</p>                    <%}else{%>                      <p class="shop-sale-info"></p>                    <%}%>                     <img width="170" height="110" src="<%=data[i].image%>" class="png24 shop-img">                     <h3><%=data[i].name%></h3>                     <%if(data[i].shopType==3){%>                     <p><%=data[i].paramMap.newPriceYear%>\u6ce2\u5e01/\u5e74</p>                     <%}else{%>                     <p><%=data[i].priceYear%>\u6ce2\u5e01/\u5e74</p>                     <%}%>                     <%if(data[i].shopType==3){%>                     <p><%=data[i].paramMap.newPriceMonth%>\u6ce2\u5e01/\u6708</p>                     <%}else{%>                     <p><%=data[i].priceMonth%>\u6ce2\u5e01/\u6708</p>                     <%}%>                     <a data-itemid="<%=data[i].itemId%>" data-method="buy" href="javascript:;" class="shop-buy-btn js-goBuy">\u8d2d\u4e70</a>                     <div class="shop-buy-more">                        <a href="javascript:;" data-effect="<%=data[i].effectUrl%>" class="js-effect">\u52a8\u6548</a><a data-itemid="<%=data[i].itemId%>" data-method="give" href="javascript:;" class="js-goBuy">\u8d60\u9001</a>                     </div>                  </li>                  <%}%>'
	};
	return i
});
define("page/home", function(e, t, n) {
	var i = e("basic/jquery");
	var r = e("common/nav/Nav");
	var s = e("common/FloatBox/FloatBox");
	var a = e("basic/Util");
	e("home/Resolution");
	e("home/ImageWall");
	e("home/HomeLogin");
	e("home/HomeSlide");
	e("home/UserCard");
	e("home/EditorReco");
	e("home/AnchorType");
	e("home/News");
	e("home/Yiren");
	e("home/RankTab");
	e("common/ToTop");
	e("home/GetBobi");
	e("home/Ewm");
	e("home/FamilyActivity");
	e("home/YearCard");
	e("home/ZhuboGold");
	e("home/top")
});