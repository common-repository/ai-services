(()=>{"use strict";var e={n:t=>{var i=t&&t.__esModule?()=>t.default:()=>t;return e.d(i,{a:i}),i},d:(t,i)=>{for(var s in i)e.o(i,s)&&!e.o(t,s)&&Object.defineProperty(t,s,{enumerable:!0,get:i[s]})},o:(e,t)=>Object.prototype.hasOwnProperty.call(e,t),r:e=>{"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})}},t={};e.r(t),e.d(t,{store:()=>P});const i=window.wp.data,s="ai-services/settings",r=window.wp.apiFetch;var n=e.n(r);const a="RECEIVE_SERVICES",o="RECEIVE_SERVICE",c={services:void 0},d={receiveServices:e=>({dispatch:t})=>{t({type:a,payload:{services:e}})},receiveService:e=>({dispatch:t})=>{t({type:o,payload:{service:e}})},refreshService:e=>async({dispatch:t,select:i})=>{if(void 0===i.getServices())return;const s=await n()({path:`/ai-services/v1/services/${e}?context=edit`});t.receiveService(s)}},S={getServices:()=>async({dispatch:e})=>{const t=await n()({path:"/ai-services/v1/services?context=edit"});e.receiveServices(t)}},g={getServices:e=>e.services,getService:(0,i.createRegistrySelector)((e=>(t,i)=>{const r=e(s).getServices();if(void 0!==r){if(void 0!==r[i])return r[i];console.error(`Invalid service ${i}.`)}}))},l={initialState:c,actions:d,reducer:function(e=c,t){switch(t.type){case a:{const{services:i}=t.payload;return{...e,services:i.reduce(((e,t)=>(e[t.slug]=t,e)),{})}}case o:{const{service:i}=t.payload;return{...e,services:{...e.services,[i.slug]:i}}}}return e},resolvers:S,selectors:g},v=l,p=window.wp.notices,u=window.wp.i18n;function f(e){return e.replace(/-|_([a-z])/g,((e,t)=>t.toUpperCase()))}const y="ais_",h="RECEIVE_SETTINGS",w="SAVE_SETTINGS_START",E="SAVE_SETTINGS_FINISH",m="SET_SETTING",_="SAVE_SETTINGS_NOTICE_ID";function b(e,t,i){const s={...e};let r=!1;return Object.keys(i).forEach((n=>{i[n]!==e[n]&&(r=!0,i[n]!==t[n]?s[n]=i[n]:delete s[n])})),r?s:e}const I={savedSettings:void 0,modifiedSettings:{},optionNameMap:{},isSavingSettings:!1},T={receiveSettings:e=>({dispatch:t})=>{t({type:h,payload:{settings:e}})},saveSettings:()=>async({dispatch:e,select:t,registry:i})=>{if(!t.areSettingsSaveable())return;const s=t.getSettings(),r={},a=[];let o;Object.keys(s).forEach((e=>{const i=t.getOptionName(e);if(!i)return void console.error(`Setting ${e} does not correspond to a WordPress option.`);if(!t.isSettingModified(e))return;r[i]=s[e];const n=i.match(/^ais_([a-z0-9-]+)_api_key$/);n&&a.push(n[1])})),await e({type:w,payload:{}});try{o=await n()({path:"/wp/v2/settings",method:"POST",data:r})}catch(e){console.error(e?.message||e)}o&&(await e.receiveSettings(o),a.forEach((t=>e.refreshService(t)))),await e({type:E,payload:{}}),o?i.dispatch(p.store).createSuccessNotice((0,u.__)("Settings successfully saved.","ai-services"),{id:_,type:"snackbar",speak:!0}):i.dispatch(p.store).createErrorNotice((0,u.__)("Saving settings failed.","ai-services"),{id:_,type:"snackbar",speak:!0})},setSetting:(e,t)=>({type:m,payload:{setting:e,value:t}}),setApiKey:(e,t)=>T.setSetting(`${f(e)}ApiKey`,t),setDeleteData:e=>T.setSetting("deleteData",e)},N={getSettings:()=>async({dispatch:e})=>{const t=await n()({path:"/wp/v2/settings"});e.receiveSettings(t)}},O={getSettings:(0,i.createSelector)((e=>{if(e.savedSettings)return{...e.savedSettings,...e.modifiedSettings}}),(e=>[e.savedSettings,e.modifiedSettings])),hasModifiedSettings:(0,i.createSelector)((e=>Object.keys(e.modifiedSettings).length>0),(e=>[e.modifiedSettings])),isSavingSettings:e=>e.isSavingSettings,areSettingsSaveable:(0,i.createRegistrySelector)((e=>()=>!e(s).isSavingSettings()&&(!!e(s).hasModifiedSettings()&&(void 0!==e(s).getSettings()&&!e(s).isResolving("getSettings"))))),getSetting:(0,i.createRegistrySelector)((e=>(t,i)=>{const r=e(s).getSettings();if(void 0!==r){if(void 0!==r[i])return r[i];console.error(`Invalid setting ${i}.`)}})),getApiKey:(e,t)=>O.getSetting(e,`${f(t)}ApiKey`),getDeleteData:e=>O.getSetting(e,"deleteData"),isSettingModified:(e,t)=>void 0!==e.modifiedSettings[t],getOptionName:(e,t)=>e.optionNameMap[t]};function k(...e){const t=e.reduce(((e,t)=>({...e,...t})),{}),i=function(e){const t=[],i={};for(let s=0;s<e.length;s++){const r=e[s];i[r]=i[r]>=1?i[r]+1:1,i[r]>1&&t.push(r)}return t}(e.reduce(((e,t)=>[...e,...Object.keys(t)]),[]));if(i.length)throw new Error(`collect() cannot accept collections with duplicate keys. Your call to collect() contains the following duplicated functions: ${i.join(", ")}. Check your data stores for duplicates.`);return t}const R=k,j=k,M=k,$=k,A=k;function C(...e){const t=[...e];let i;return"function"!=typeof t[0]&&(i=t.shift()),(e=i,s={})=>t.reduce(((e,t)=>t(e,s)),e)}function V(e){return e}const D=function(...e){const t=A(...e.map((e=>e.initialState||{})));return{initialState:t,controls:j(...e.map((e=>e.controls||{}))),actions:R(...e.map((e=>e.actions||{}))),reducer:C(t,...e.map((e=>e.reducer||V))),resolvers:M(...e.map((e=>e.resolvers||{}))),selectors:$(...e.map((e=>e.selectors||{})))}}(v,{initialState:I,actions:T,reducer:function(e=I,t){switch(t.type){case h:{const{settings:i}=t.payload,s={},r={};return Object.keys(i).forEach((e=>{if(!e.startsWith(y))return;const t=f(e.replace(y,""));s[t]=i[e],r[t]=e})),{...e,savedSettings:s,modifiedSettings:{},optionNameMap:r}}case w:return{...e,isSavingSettings:!0};case E:return{...e,isSavingSettings:!1};case m:{const{setting:i,value:s}=t.payload;return void 0===e.savedSettings?(console.error(`Setting ${i} cannot be set before settings are loaded.`),e):void 0===e.savedSettings[i]?(console.error(`Invalid setting ${i}.`),e):{...e,modifiedSettings:b(e.modifiedSettings,e.savedSettings,{[i]:s})}}}return e},resolvers:N,selectors:O}),P=(0,i.createReduxStore)(s,D);(0,i.register)(P),(window.aiServices=window.aiServices||{}).settingsStore=t})();