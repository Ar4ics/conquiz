(window.webpackJsonp=window.webpackJsonp||[]).push([[0],{"+lRy":function(t,e){},0:function(t,e,n){n("JO1w"),t.exports=n("+lRy")},"7xA0":function(t,e,n){var s=n("ctS3");"string"==typeof s&&(s=[[t.i,s,""]]);var o={hmr:!0,transform:void 0,insertInto:void 0};n("aET+")(s,o);s.locals&&(t.exports=s.locals)},"8oxB":function(t,e){var n,s,o=t.exports={};function r(){throw new Error("setTimeout has not been defined")}function i(){throw new Error("clearTimeout has not been defined")}function a(t){if(n===setTimeout)return setTimeout(t,0);if((n===r||!n)&&setTimeout)return n=setTimeout,setTimeout(t,0);try{return n(t,0)}catch(e){try{return n.call(null,t,0)}catch(e){return n.call(this,t,0)}}}!function(){try{n="function"==typeof setTimeout?setTimeout:r}catch(t){n=r}try{s="function"==typeof clearTimeout?clearTimeout:i}catch(t){s=i}}();var c,l=[],u=!1,d=-1;function f(){u&&c&&(u=!1,c.length?l=c.concat(l):d=-1,l.length&&m())}function m(){if(!u){var t=a(f);u=!0;for(var e=l.length;e;){for(c=l,l=[];++d<e;)c&&c[d].run();d=-1,e=l.length}c=null,u=!1,function(t){if(s===clearTimeout)return clearTimeout(t);if((s===i||!s)&&clearTimeout)return s=clearTimeout,clearTimeout(t);try{s(t)}catch(e){try{return s.call(null,t)}catch(e){return s.call(this,t)}}}(t)}}function h(t,e){this.fun=t,this.array=e}function v(){}o.nextTick=function(t){var e=new Array(arguments.length-1);if(arguments.length>1)for(var n=1;n<arguments.length;n++)e[n-1]=arguments[n];l.push(new h(t,e)),1!==l.length||u||a(m)},h.prototype.run=function(){this.fun.apply(null,this.array)},o.title="browser",o.browser=!0,o.env={},o.argv=[],o.version="",o.versions={},o.on=v,o.addListener=v,o.once=v,o.off=v,o.removeListener=v,o.removeAllListeners=v,o.emit=v,o.prependListener=v,o.prependOnceListener=v,o.listeners=function(t){return[]},o.binding=function(t){throw new Error("process.binding is not supported")},o.cwd=function(){return"/"},o.chdir=function(t){throw new Error("process.chdir is not supported")},o.umask=function(){return 0}},"9tPo":function(t,e){t.exports=function(t){var e="undefined"!=typeof window&&window.location;if(!e)throw new Error("fixUrls requires window.location");if(!t||"string"!=typeof t)return t;var n=e.protocol+"//"+e.host,s=n+e.pathname.replace(/\/[^\/]*$/,"/");return t.replace(/url\s*\(((?:[^)(]|\((?:[^)(]+|\([^)(]*\))*\))*)\)/gi,function(t,e){var o,r=e.trim().replace(/^"(.*)"$/,function(t,e){return e}).replace(/^'(.*)'$/,function(t,e){return e});return/^(#|data:|http:\/\/|https:\/\/|file:\/\/\/|\s*$)/i.test(r)?t:(o=0===r.indexOf("//")?r:0===r.indexOf("/")?n+r:s+r.replace(/^\.\//,""),"url("+JSON.stringify(o)+")")})}},BEtg:function(t,e){function n(t){return!!t.constructor&&"function"==typeof t.constructor.isBuffer&&t.constructor.isBuffer(t)}t.exports=function(t){return null!=t&&(n(t)||function(t){return"function"==typeof t.readFloatLE&&"function"==typeof t.slice&&n(t.slice(0,0))}(t)||!!t._isBuffer)}},Ei1z:function(t,e,n){(t.exports=n("I1BE")(!1)).push([t.i,"\n.main[data-v-1c05bbb3] {\n    border: 0.05rem solid;\n}\n.player-square[data-v-1c05bbb3] {\n    height: 0.05rem;\n}\n.box[data-v-1c05bbb3] {\n    border: 0.05rem solid;\n    height: 10rem;\n    background-color: white;\n}\n",""])},HijD:function(t,e,n){"use strict";n.r(e),function(t){var e=n("Vjiq");window._=n("LvDl");try{window.$=n("EVdn"),window.Popper=n("+G6p").default,n("SYky")}catch(t){}window.axios=n("vDqi"),window.axios.defaults.headers.common["X-Requested-With"]="XMLHttpRequest";var s=document.head.querySelector('meta[name="csrf-token"]');s?window.axios.defaults.headers.common["X-CSRF-TOKEN"]=s.content:console.error("CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token"),window.Pusher=n("eC5B"),window.Echo=new e.default({broadcaster:"pusher",key:"2e51ca3bb13d5c649395",cluster:"eu",encrypted:!0})}.call(this,n("EVdn"))},I1BE:function(t,e){t.exports=function(t){var e=[];return e.toString=function(){return this.map(function(e){var n=function(t,e){var n=t[1]||"",s=t[3];if(!s)return n;if(e&&"function"==typeof btoa){var o=(i=s,"/*# sourceMappingURL=data:application/json;charset=utf-8;base64,"+btoa(unescape(encodeURIComponent(JSON.stringify(i))))+" */"),r=s.sources.map(function(t){return"/*# sourceURL="+s.sourceRoot+t+" */"});return[n].concat(r).concat([o]).join("\n")}var i;return[n].join("\n")}(e,t);return e[2]?"@media "+e[2]+"{"+n+"}":n}).join("")},e.i=function(t,n){"string"==typeof t&&(t=[[null,t,""]]);for(var s={},o=0;o<this.length;o++){var r=this[o][0];"number"==typeof r&&(s[r]=!0)}for(o=0;o<t.length;o++){var i=t[o];"number"==typeof i[0]&&s[i[0]]||(n&&!i[2]?i[2]=n:n&&(i[2]="("+i[2]+") and ("+n+")"),e.push(i))}},e}},JO1w:function(t,e,n){"use strict";n.r(e);var s=n("XuX8"),o=n.n(s),r=n("7pj7"),i=n.n(r),a=n("Y2xv").a,c=(n("nOJH"),n("KHd+")),l=Object(c.a)(a,function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("div",{staticClass:"container"},[n("div",{staticClass:"card bg-light"},[n("h5",{staticClass:"card-header text-center"},[t._v(t._s(t.game.title))]),t._v(" "),n("div",{staticClass:"card-body"},t._l(t.game.user_colors,function(e,s){return n("div",{key:s.id,staticClass:"row no-gutters"},[n("div",{staticClass:"col-8 col-md-10"},[t._v(t._s(e.user.name))]),t._v(" "),n("div",{staticClass:"col-4 col-md-2 player-square",style:{"background-color":e.color}},[t._v(t._s(e.score)+"\n                    баллов\n                ")])])}))]),t._v(" "),n("div",{staticClass:"card bg-white"},[n("h5",{staticClass:"card-header text-center"},[t._v("Online-пользователи")]),t._v(" "),n("div",{staticClass:"card-body"},t._l(t.online_users,function(e,s){return n("span",{key:s.id,staticClass:"row no-gutters"},[t._v("\n                "+t._s(e.name)+"\n            ")])}))]),t._v(" "),n("div",{staticClass:"card bg-white"},[n("h5",{staticClass:"card-header text-center"},[t._v("Чат")]),t._v(" "),n("div",{staticClass:"card-body"},[n("chat-messages",{attrs:{game_id:t.game.id}})],1)]),t._v(" "),t.question?n("div",[n("div",{staticClass:"card text-center"},[n("h6",{staticClass:"card-header"},[t._v("\n                Вопрос\n            ")]),t._v(" "),n("div",{staticClass:"card-body"},[n("p",[t._v(t._s(t.question.title))]),t._v(" "),n("div",{staticClass:"a"},t._l(t.question.answers,function(e,s){return n("button",{key:s,staticClass:"btn btn-light",attrs:{id:"a-"+s},on:{click:function(e){t.answer(s)}}},[t._v("\n                        "+t._s(e)+"\n                    ")])})),t._v(" "),t.answers.length>0?n("div",t._l(t.answers,function(e,s){return n("div",{key:s},[t._v("\n                        "+t._s(e.name)+" дал "+t._s(e.is_correct?"правильный":"неправильный")+" "+t._s(e.ans)+" ответ\n                    ")])})):t._e()])])]):n("div",[t.winner?n("div",{staticClass:"text-center"},[n("p",{staticClass:"alert alert-success"},[t._v("Игра завершена. Победитель "+t._s(t.winner.name))])]):n("div",[t.move?n("div",{staticClass:"text-center"},[n("p",{staticClass:"alert alert-primary"},[t._v("Ждем хода игрока "+t._s(t.move.name))])]):t._e()])]),t._v(" "),n("div",{staticClass:"main"},t._l(t.game.count_y,function(e,s){return n("div",{key:s,staticClass:"row no-gutters"},t._l(t.game.count_x,function(e,o){return n("div",{key:o,staticClass:"box",class:t.review(o,s),on:{click:function(e){t.clickBox(o,s)}}})}))}))])},[],!1,null,"1c05bbb3",null);l.options.__file="Game.vue";var u=l.exports,d={props:["initialGames","user"],data:function(){return{current_games:[],finished_games:[]}},mounted:function(){this.current_games=this.initialGames.filter(function(t){return!t.stage3_has_finished}),this.finished_games=this.initialGames.filter(function(t){return t.stage3_has_finished}),this.listenForNewGroups()},methods:{listenForNewGroups:function(){var t=this;Echo.private("games").listen("GameCreated",function(e){console.log(e),t.current_games.push(e)})},watchGame:function(t){window.location.href="/games/"+t}}},f=Object(c.a)(d,function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("div",[n("div",{staticClass:"card"},[n("div",{staticClass:"card-header text-center"},[t._v("Общий чат")]),t._v(" "),n("div",{staticClass:"card-body"},[n("chat-messages",{attrs:{game_id:0}})],1)]),t._v(" "),n("div",{staticClass:"card"},[n("div",{staticClass:"card-header text-center"},[t._v("Текущие игры")]),t._v(" "),n("div",{staticClass:"card-body"},[n("table",{staticClass:"table table-hover table-bordered"},[t._m(0),t._v(" "),n("tbody",t._l(t.current_games,function(e,s){return n("tr",{key:e.id,on:{click:function(n){t.watchGame(e.id)}}},[n("th",{attrs:{scope:"row"}},[t._v(t._s(s+1))]),t._v(" "),n("td",[t._v(t._s(e.title))]),t._v(" "),n("td",[t._v(t._s(e.user_colors_count))])])}))])])]),t._v(" "),n("div",{staticClass:"card"},[n("div",{staticClass:"card-header text-center"},[t._v("Завершенные игры")]),t._v(" "),n("div",{staticClass:"card-body"},[n("table",{staticClass:"table table-hover table-bordered"},[t._m(1),t._v(" "),n("tbody",t._l(t.finished_games,function(e,s){return n("tr",{key:e.id,on:{click:function(n){t.watchGame(e.id)}}},[n("th",{attrs:{scope:"row"}},[t._v(t._s(s+1))]),t._v(" "),n("td",[t._v(t._s(e.title))]),t._v(" "),n("td",[t._v(t._s(e.winner.user.name))])])}))])])])])},[function(){var t=this.$createElement,e=this._self._c||t;return e("thead",{staticClass:"thead-light"},[e("tr",[e("th",{attrs:{scope:"col"}},[this._v("#")]),this._v(" "),e("th",{attrs:{scope:"col"}},[this._v("Комната")]),this._v(" "),e("th",{attrs:{scope:"col"}},[this._v("Игроков")])])])},function(){var t=this.$createElement,e=this._self._c||t;return e("thead",{staticClass:"thead-light"},[e("tr",[e("th",{attrs:{scope:"col"}},[this._v("#")]),this._v(" "),e("th",{attrs:{scope:"col"}},[this._v("Комната")]),this._v(" "),e("th",{attrs:{scope:"col"}},[this._v("Победитель")])])])}],!1,null,null,null);f.options.__file="Games.vue";var m=f.exports,h={props:["initialUsers"],data:function(){return{title:"",count_x:2,count_y:2,users:[],online_users:this.initialUsers}},mounted:function(){var t=this;Echo.join("users").here(function(e){console.log("users",e),e.forEach(function(e){var n=t.online_users.find(function(t){return t.id===e.id});n&&(n.status="online")}),t.$notify({text:"В зале ".concat(e.length," человек")})}).joining(function(e){console.log("joining",e);var n=t.online_users.find(function(t){return t.id===e.id});n&&(n.status="online"),t.$notify({text:"В зал зашел ".concat(e.name)})}).leaving(function(e){console.log("leaving",e);var n=t.online_users.find(function(t){return t.id===e.id});n&&(n.status="offline"),t.$notify({text:"Из зала вышел ".concat(e.name)})})},computed:{online_sorted:function(){return this.online_users.sort(function(t,e){return t.status>e.status?-1:t.status<e.status?1:0})}},methods:{createGroup:function(){var t=this;this.users.length<1||this.users.length>2?this.$notify({text:"Выберите одного или двух игроков"}):""!==this.title.trim()?axios.post("/games",{title:this.title,users:this.users,count_x:this.count_x,count_y:this.count_y}).then(function(e){t.title="",t.users=[],t.$notify({text:"Игра создана"})}):this.$notify({text:"Введите название игры"})}}},v=Object(c.a)(h,function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("div",{staticClass:"card"},[n("div",{staticClass:"card-header text-center"},[t._v("Создание игровой комнаты")]),t._v(" "),n("div",{staticClass:"card-body"},[n("form",[n("div",{staticClass:"form-group"},[n("label",{attrs:{for:"title"}},[t._v("Название игры")]),t._v(" "),n("input",{directives:[{name:"model",rawName:"v-model",value:t.title,expression:"title"}],staticClass:"form-control",attrs:{id:"title",type:"text",required:""},domProps:{value:t.title},on:{input:function(e){e.target.composing||(t.title=e.target.value)}}})]),t._v(" "),n("div",{staticClass:"form-group"},[n("label",{attrs:{for:"x"}},[t._v("Длина поля по x")]),t._v(" "),n("input",{directives:[{name:"model",rawName:"v-model",value:t.count_x,expression:"count_x"}],staticClass:"form-control",attrs:{id:"x",min:"2",max:"4",type:"number"},domProps:{value:t.count_x},on:{input:function(e){e.target.composing||(t.count_x=e.target.value)}}})]),t._v(" "),n("div",{staticClass:"form-group"},[n("label",{attrs:{for:"y"}},[t._v("Длина поля по y")]),t._v(" "),n("input",{directives:[{name:"model",rawName:"v-model",value:t.count_y,expression:"count_y"}],staticClass:"form-control",attrs:{id:"y",min:"2",max:"4",type:"number"},domProps:{value:t.count_y},on:{input:function(e){e.target.composing||(t.count_y=e.target.value)}}})]),t._v(" "),n("div",{staticClass:"form-group"},[n("label",{attrs:{for:"users"}},[t._v("Выберите пользователей...")]),t._v(" "),n("select",{directives:[{name:"model",rawName:"v-model",value:t.users,expression:"users"}],staticClass:"form-control",attrs:{multiple:"",id:"users"},on:{change:function(e){var n=Array.prototype.filter.call(e.target.options,function(t){return t.selected}).map(function(t){return"_value"in t?t._value:t.value});t.users=e.target.multiple?n:n[0]}}},t._l(t.online_sorted,function(e){return n("option",{key:e.id,domProps:{value:e.id}},[t._v("\n                        "+t._s(e.name)+" - "+t._s(e.status)+"\n                    ")])}))]),t._v(" "),n("div",{staticClass:"form-group text-center"},[n("button",{staticClass:"btn btn-primary",attrs:{type:"submit"},on:{click:function(e){return e.preventDefault(),t.createGroup(e)}}},[t._v("Создать")])])])])])},[],!1,null,null,null);v.options.__file="CreateGame.vue";var p=v.exports,g={props:["chatMessage"],data:function(){return{message:this.chatMessage.message}},computed:{messageEmoji:function(){var t=this.message;return t=(t=(t=(t=(t=(t=(t=t.replace("капа",'<img alt="kappa" src="/img/kappa.png"/>')).replace("вин",'<img alt="win" src="/img/win.png"/>')).replace("кеепо",'<img alt="win" src="/img/keepo.png"/>')).replace("росс",'<img alt="win" src="/img/ross.png"/>')).replace("чит",'<img alt="win" src="/img/cheat.png"/>')).replace("де",'<img alt="win" src="/img/de.png"/>')).replace("хил",'<img alt="win" src="/img/hil.png"/>')}}},_=(n("mzES"),Object(c.a)(g,function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("div",{staticClass:"row no-gutters"},[n("div",{staticClass:"col-6 col-md-10"},[n("span",{staticClass:"chat-name"},[t._v(t._s(t.chatMessage.user.name))]),t._v(" "),n("span",{domProps:{innerHTML:t._s(t.messageEmoji)}})]),t._v(" "),n("div",{staticClass:"col-6 col-md-2 text-right"},[n("small",[t._v(t._s(t.chatMessage.date))])])])},[],!1,null,"1f269906",null));_.options.__file="ChatMessage.vue";var y=_.exports,b={props:["game_id"],data:function(){return{chat_message:"",messages:[]}},mounted:function(){this.listenForEvents(),this.loadMessages()},updated:function(){var t=this;this.$nextTick(function(){return t.scrollToEnd()})},methods:{loadMessages:function(){var t=this;axios.get("/games/"+this.game_id+"/message").then(function(e){console.log(e),e.data.error&&t.$notify({text:e.data.error}),t.messages=e.data})},submitMessage:function(){var t=this;console.log(this.chat_message),axios.post("/games/"+this.game_id+"/message",{message:this.chat_message}).then(function(e){console.log(e),e.data.error&&t.$notify({text:e.data.error})}),this.chat_message=""},listenForEvents:function(){var t=this;Echo.private("game."+this.game_id).listen("GameMessageCreated",function(e){console.log("GameMessageCreated",e),t.messages.push(e)})},scrollToEnd:function(){this.$el.scrollTop=this.$el.lastElementChild.offsetTop}}},w=(n("wWYS"),Object(c.a)(b,function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("div",{staticClass:"chat-content"},[t._l(t.messages,function(t,e){return n("div",{key:e.id},[n("chat-message",{attrs:{chatMessage:t}})],1)}),t._v(" "),n("div",{staticStyle:{"padding-top":"10px"}},[n("input",{directives:[{name:"model",rawName:"v-model",value:t.chat_message,expression:"chat_message"}],staticClass:"form-control",attrs:{type:"text",placeholder:"Сообщение"},domProps:{value:t.chat_message},on:{keyup:function(e){return"button"in e||!t._k(e.keyCode,"enter",13,e.key,"Enter")?t.submitMessage(e):null},input:function(e){e.target.composing||(t.chat_message=e.target.value)}}})])],2)},[],!1,null,"7f8c7518",null));w.options.__file="ChatMessages.vue";var x=w.exports;n("HijD"),window.Vue=o.a,o.a.use(i.a),window.Bus=new o.a,o.a.component("games",m),o.a.component("game",u),o.a.component("create-game",p),o.a.component("chat-message",y),o.a.component("chat-messages",x);new o.a({el:"#app"})},URgk:function(t,e,n){(function(t){var s=void 0!==t&&t||"undefined"!=typeof self&&self||window,o=Function.prototype.apply;function r(t,e){this._id=t,this._clearFn=e}e.setTimeout=function(){return new r(o.call(setTimeout,s,arguments),clearTimeout)},e.setInterval=function(){return new r(o.call(setInterval,s,arguments),clearInterval)},e.clearTimeout=e.clearInterval=function(t){t&&t.close()},r.prototype.unref=r.prototype.ref=function(){},r.prototype.close=function(){this._clearFn.call(s,this._id)},e.enroll=function(t,e){clearTimeout(t._idleTimeoutId),t._idleTimeout=e},e.unenroll=function(t){clearTimeout(t._idleTimeoutId),t._idleTimeout=-1},e._unrefActive=e.active=function(t){clearTimeout(t._idleTimeoutId);var e=t._idleTimeout;e>=0&&(t._idleTimeoutId=setTimeout(function(){t._onTimeout&&t._onTimeout()},e))},n("YBdB"),e.setImmediate="undefined"!=typeof self&&self.setImmediate||void 0!==t&&t.setImmediate||this&&this.setImmediate,e.clearImmediate="undefined"!=typeof self&&self.clearImmediate||void 0!==t&&t.clearImmediate||this&&this.clearImmediate}).call(this,n("yLpj"))},Y2xv:function(t,e,n){"use strict";(function(t){e.a={props:["gameData","player","boxes","whoMoves","initialQuestion","competitiveBox"],data:function(){return{move:{id:"",name:""},game:{count_x:0,count_y:0,winner_user_color_id:0,user_colors:[]},answers:[],count_col:0,question:this.initialQuestion,common_box:this.competitiveBox,winner:null,online_users:[]}},created:function(){var t=this;if(this.game=this.gameData,this.move=this.whoMoves,this.game.winner_user_color_id){var e=this.game.user_colors.filter(function(e){return e.id===t.game.winner_user_color_id})[0];this.winner=e.user}this.count_col=12/this.game.count_x},mounted:function(){var e=this;this.listenForEvents(),this.player||this.$notify({text:"Вы зашли как гость"}),this.boxes.forEach(function(e){t(".b-".concat(e.x,"-").concat(e.y)).css("background-color",e.color)}),this.common_box&&t(".b-".concat(this.common_box.x,"-").concat(this.common_box.y)).css("background-color","grey"),Echo.join("game_users."+this.game.id).here(function(t){console.log("users",t),e.online_users=t,e.$notify({text:"В комнате ".concat(t.length," человек")})}).joining(function(t){console.log("joining",t),e.online_users.push(t),e.$notify({text:"В комнату зашел ".concat(t.name)})}).leaving(function(t){console.log("leaving",t),e.online_users=e.online_users.filter(function(e){return e.id!==t.id}),e.$notify({text:"Из комнаты вышел ".concat(t.name)})})},methods:{review:function(t,e){return"col-"+this.count_col+" b-"+t+"-"+e},clickBox:function(t,e){var n=this;if(this.player){var s=this.player.id;axios.post("/games/"+this.game.id+"/box/clicked",{x:t,y:e,userColorId:s}).then(function(t){t.data.error&&n.$notify({text:t.data.error})})}else this.$notify({text:"Вы зашли как гость"})},answer:function(t){var e=this;if(console.log("answer",t),this.player){var n=this.player.id,s=this.question.id;axios.post("/games/"+this.game.id+"/user/answered",{userAnswer:t,userColorId:n,questionId:s}).then(function(t){t.data.error&&e.$notify({text:t.data.error})})}else this.$notify({text:"Вы зашли как гость"})},listenForEvents:function(){var e=this;Echo.private("game."+this.game.id).listen("BoxClicked",function(e){console.log("box clicked",e),t(".b-".concat(e.x,"-").concat(e.y)).css("background-color",e.color)}).listen("ShowCompetitiveBox",function(e){console.log("box clicked",e),t(".b-".concat(e.x,"-").concat(e.y)).css("background-color","grey")}).listen("WhoMoves",function(t){console.log("who moves",t),e.move=t}).listen("WinnerFound",function(t){console.log("winner",t);var n=e.game.user_colors.filter(function(e){return e.id===t.winner.id})[0];e.winner=n.user}).listen("NewQuestion",function(t){console.log("new question",t),e.question=t}).listen("AnswersResults",function(n){console.log("answer results",n),n.results.forEach(function(t){var n=e.game.user_colors.filter(function(e){return e.id===t.user_color_id})[0];n.score=t.score,e.answers.push({name:n.user.name,ans:t.answer+1,is_correct:t.is_correct})}),t("#a-".concat(n.correct)).attr("class","btn btn-success"),n.boxes.forEach(function(e){t(".b-".concat(e.x,"-").concat(e.y)).css("background-color","white")}),setTimeout(function(){e.answers=[],e.question=null},5e3)})}}}}).call(this,n("EVdn"))},YBdB:function(t,e,n){(function(t,e){!function(t,n){"use strict";if(!t.setImmediate){var s,o,r,i,a,c=1,l={},u=!1,d=t.document,f=Object.getPrototypeOf&&Object.getPrototypeOf(t);f=f&&f.setTimeout?f:t,"[object process]"==={}.toString.call(t.process)?s=function(t){e.nextTick(function(){h(t)})}:!function(){if(t.postMessage&&!t.importScripts){var e=!0,n=t.onmessage;return t.onmessage=function(){e=!1},t.postMessage("","*"),t.onmessage=n,e}}()?t.MessageChannel?((r=new MessageChannel).port1.onmessage=function(t){h(t.data)},s=function(t){r.port2.postMessage(t)}):d&&"onreadystatechange"in d.createElement("script")?(o=d.documentElement,s=function(t){var e=d.createElement("script");e.onreadystatechange=function(){h(t),e.onreadystatechange=null,o.removeChild(e),e=null},o.appendChild(e)}):s=function(t){setTimeout(h,0,t)}:(i="setImmediate$"+Math.random()+"$",a=function(e){e.source===t&&"string"==typeof e.data&&0===e.data.indexOf(i)&&h(+e.data.slice(i.length))},t.addEventListener?t.addEventListener("message",a,!1):t.attachEvent("onmessage",a),s=function(e){t.postMessage(i+e,"*")}),f.setImmediate=function(t){"function"!=typeof t&&(t=new Function(""+t));for(var e=new Array(arguments.length-1),n=0;n<e.length;n++)e[n]=arguments[n+1];var o={callback:t,args:e};return l[c]=o,s(c),c++},f.clearImmediate=m}function m(t){delete l[t]}function h(t){if(u)setTimeout(h,0,t);else{var e=l[t];if(e){u=!0;try{!function(t){var e=t.callback,s=t.args;switch(s.length){case 0:e();break;case 1:e(s[0]);break;case 2:e(s[0],s[1]);break;case 3:e(s[0],s[1],s[2]);break;default:e.apply(n,s)}}(e)}finally{m(t),u=!1}}}}}("undefined"==typeof self?void 0===t?this:t:self)}).call(this,n("yLpj"),n("8oxB"))},YuTi:function(t,e){t.exports=function(t){return t.webpackPolyfill||(t.deprecate=function(){},t.paths=[],t.children||(t.children=[]),Object.defineProperty(t,"loaded",{enumerable:!0,get:function(){return t.l}}),Object.defineProperty(t,"id",{enumerable:!0,get:function(){return t.i}}),t.webpackPolyfill=1),t}},"aET+":function(t,e,n){var s,o,r={},i=(s=function(){return window&&document&&document.all&&!window.atob},function(){return void 0===o&&(o=s.apply(this,arguments)),o}),a=function(t){var e={};return function(t,n){if("function"==typeof t)return t();if(void 0===e[t]){var s=function(t,e){return e?e.querySelector(t):document.querySelector(t)}.call(this,t,n);if(window.HTMLIFrameElement&&s instanceof window.HTMLIFrameElement)try{s=s.contentDocument.head}catch(t){s=null}e[t]=s}return e[t]}}(),c=null,l=0,u=[],d=n("9tPo");function f(t,e){for(var n=0;n<t.length;n++){var s=t[n],o=r[s.id];if(o){o.refs++;for(var i=0;i<o.parts.length;i++)o.parts[i](s.parts[i]);for(;i<s.parts.length;i++)o.parts.push(_(s.parts[i],e))}else{var a=[];for(i=0;i<s.parts.length;i++)a.push(_(s.parts[i],e));r[s.id]={id:s.id,refs:1,parts:a}}}}function m(t,e){for(var n=[],s={},o=0;o<t.length;o++){var r=t[o],i=e.base?r[0]+e.base:r[0],a={css:r[1],media:r[2],sourceMap:r[3]};s[i]?s[i].parts.push(a):n.push(s[i]={id:i,parts:[a]})}return n}function h(t,e){var n=a(t.insertInto);if(!n)throw new Error("Couldn't find a style target. This probably means that the value for the 'insertInto' parameter is invalid.");var s=u[u.length-1];if("top"===t.insertAt)s?s.nextSibling?n.insertBefore(e,s.nextSibling):n.appendChild(e):n.insertBefore(e,n.firstChild),u.push(e);else if("bottom"===t.insertAt)n.appendChild(e);else{if("object"!=typeof t.insertAt||!t.insertAt.before)throw new Error("[Style Loader]\n\n Invalid value for parameter 'insertAt' ('options.insertAt') found.\n Must be 'top', 'bottom', or Object.\n (https://github.com/webpack-contrib/style-loader#insertat)\n");var o=a(t.insertAt.before,n);n.insertBefore(e,o)}}function v(t){if(null===t.parentNode)return!1;t.parentNode.removeChild(t);var e=u.indexOf(t);e>=0&&u.splice(e,1)}function p(t){var e=document.createElement("style");if(void 0===t.attrs.type&&(t.attrs.type="text/css"),void 0===t.attrs.nonce){var s=function(){0;return n.nc}();s&&(t.attrs.nonce=s)}return g(e,t.attrs),h(t,e),e}function g(t,e){Object.keys(e).forEach(function(n){t.setAttribute(n,e[n])})}function _(t,e){var n,s,o,r;if(e.transform&&t.css){if(!(r="function"==typeof e.transform?e.transform(t.css):e.transform.default(t.css)))return function(){};t.css=r}if(e.singleton){var i=l++;n=c||(c=p(e)),s=w.bind(null,n,i,!1),o=w.bind(null,n,i,!0)}else t.sourceMap&&"function"==typeof URL&&"function"==typeof URL.createObjectURL&&"function"==typeof URL.revokeObjectURL&&"function"==typeof Blob&&"function"==typeof btoa?(n=function(t){var e=document.createElement("link");return void 0===t.attrs.type&&(t.attrs.type="text/css"),t.attrs.rel="stylesheet",g(e,t.attrs),h(t,e),e}(e),s=function(t,e,n){var s=n.css,o=n.sourceMap,r=void 0===e.convertToAbsoluteUrls&&o;(e.convertToAbsoluteUrls||r)&&(s=d(s));o&&(s+="\n/*# sourceMappingURL=data:application/json;base64,"+btoa(unescape(encodeURIComponent(JSON.stringify(o))))+" */");var i=new Blob([s],{type:"text/css"}),a=t.href;t.href=URL.createObjectURL(i),a&&URL.revokeObjectURL(a)}.bind(null,n,e),o=function(){v(n),n.href&&URL.revokeObjectURL(n.href)}):(n=p(e),s=function(t,e){var n=e.css,s=e.media;s&&t.setAttribute("media",s);if(t.styleSheet)t.styleSheet.cssText=n;else{for(;t.firstChild;)t.removeChild(t.firstChild);t.appendChild(document.createTextNode(n))}}.bind(null,n),o=function(){v(n)});return s(t),function(e){if(e){if(e.css===t.css&&e.media===t.media&&e.sourceMap===t.sourceMap)return;s(t=e)}else o()}}t.exports=function(t,e){if("undefined"!=typeof DEBUG&&DEBUG&&"object"!=typeof document)throw new Error("The style-loader cannot be used in a non-browser environment");(e=e||{}).attrs="object"==typeof e.attrs?e.attrs:{},e.singleton||"boolean"==typeof e.singleton||(e.singleton=i()),e.insertInto||(e.insertInto="head"),e.insertAt||(e.insertAt="bottom");var n=m(t,e);return f(n,e),function(t){for(var s=[],o=0;o<n.length;o++){var i=n[o];(a=r[i.id]).refs--,s.push(a)}t&&f(m(t,e),e);for(o=0;o<s.length;o++){var a;if(0===(a=s[o]).refs){for(var c=0;c<a.parts.length;c++)a.parts[c]();delete r[a.id]}}}};var y,b=(y=[],function(t,e){return y[t]=e,y.filter(Boolean).join("\n")});function w(t,e,n,s){var o=n?"":s.css;if(t.styleSheet)t.styleSheet.cssText=b(e,o);else{var r=document.createTextNode(o),i=t.childNodes;i[e]&&t.removeChild(i[e]),i.length?t.insertBefore(r,i[e]):t.appendChild(r)}}},ctS3:function(t,e,n){(t.exports=n("I1BE")(!1)).push([t.i,"\n.chat-content[data-v-7f8c7518] {\n    overflow-y: scroll;\n    max-height: 200px;\n}\n[data-v-7f8c7518]::-webkit-scrollbar {\n    width: 0px;\n    background: transparent; /* make scrollbar transparent */\n}\n",""])},mXbC:function(t,e,n){var s=n("Ei1z");"string"==typeof s&&(s=[[t.i,s,""]]);var o={hmr:!0,transform:void 0,insertInto:void 0};n("aET+")(s,o);s.locals&&(t.exports=s.locals)},mzES:function(t,e,n){"use strict";var s=n("uDu2");n.n(s).a},nOJH:function(t,e,n){"use strict";var s=n("mXbC");n.n(s).a},rVg7:function(t,e,n){(t.exports=n("I1BE")(!1)).push([t.i,"\n.chat-name[data-v-1f269906] {\n    padding: 1px;\n    border-style: groove;\n    border-width: 1px;\n}\n\n",""])},uDu2:function(t,e,n){var s=n("rVg7");"string"==typeof s&&(s=[[t.i,s,""]]);var o={hmr:!0,transform:void 0,insertInto:void 0};n("aET+")(s,o);s.locals&&(t.exports=s.locals)},wWYS:function(t,e,n){"use strict";var s=n("7xA0");n.n(s).a},yLpj:function(t,e){var n;n=function(){return this}();try{n=n||new Function("return this")()}catch(t){"object"==typeof window&&(n=window)}t.exports=n}},[[0,1,2]]]);