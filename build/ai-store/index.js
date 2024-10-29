(()=>{"use strict";var e={n:t=>{var r=t&&t.__esModule?()=>t.default:()=>t;return e.d(r,{a:r}),r},d:(t,r)=>{for(var s in r)e.o(r,s)&&!e.o(t,s)&&Object.defineProperty(t,s,{enumerable:!0,get:r[s]})},o:(e,t)=>Object.prototype.hasOwnProperty.call(e,t),r:e=>{"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})}},t={};e.r(t),e.d(t,{store:()=>$});const r=window.wp.data,s="ai-services/ai",i=window.wp.apiFetch;var a=e.n(i);const o=window.wp.i18n;let n;function c(e){if("string"==typeof e)return{role:"user",parts:[{text:e}]};if(Array.isArray(e))return e[0].role||e[0].parts?e:{role:"user",parts:e};if(!e.role||!e.parts)throw new Error((0,o.__)("The value must be a string, a parts object, or a content object.","ai-services"));return e}function l(e){if(!e)throw new Error((0,o.__)("The content argument is required to generate content.","ai-services"));if(!Array.isArray(e))if("object"==typeof e){if(!e.role||!e.parts)throw new Error((0,o.__)("The content object must have a role and parts properties.","ai-services"))}else if("string"!=typeof e)throw new Error((0,o.__)("The content argument must be a string, an object, or an array of objects.","ai-services"))}class h{constructor({slug:e,name:t,capabilities:r,available_models:s}){if(!s||!Object.keys(s).length)throw new Error(`No models available for the service ${e}. Is it available?`);this.slug=e,this.name=t,this.capabilities=r,this.models=s}getServiceSlug(){return this.slug}getCapabilities(){return this.capabilities}listModels(){return this.models}async generateText(e,t){if(!this.capabilities.includes("text_generation"))throw new Error((0,o.__)("The service does not support text generation.","ai-services"));if(!t?.feature)throw new Error((0,o.__)('You must provide a "feature" identifier as part of the model parameters, which only contains lowercase letters, numbers, and hyphens.',"ai-services"));t?.capabilities||(t={...t,capabilities:["text_generation"]}),l(e);try{return await a()({path:`/ai-services/v1/services/${this.slug}:generate-text`,method:"POST",data:{content:e,model_params:t||{}}})}catch(e){throw new Error(e.message||e.code||e)}}startChat(e,t){if(!this.capabilities.includes("text_generation"))throw new Error((0,o.__)("The service does not support text generation.","ai-services"));return new d(this,{history:e,modelParams:t})}}class u extends h{async generateText(e,t){if(!this.capabilities.includes("text_generation"))throw new Error((0,o.__)("The service does not support text generation.","ai-services"));if(!t?.feature)throw new Error((0,o.__)('You must provide a "feature" identifier as part of the model parameters, which only contains lowercase letters, numbers, and hyphens.',"ai-services"));if(l(e),"string"!=typeof e)if(Array.isArray(e)){let t;if((e[0].role||e[0].parts)&&e.length>1)throw new Error("The browser service does not support history at this time.");t=e[0].role||e[0].parts?e[0].parts:e,e=t.map((e=>e.text||"")).join("\n")}else"object"==typeof e&&(e=e.parts.map((e=>e.text||"")).join("\n"));const r=await window.ai.assistant.create(t);return[{content:{role:"model",parts:[{text:await r.prompt(e)}]}}]}}class d{constructor(e,{history:t,modelParams:r}){this.service=e,this.modelParams=r,t?(function(e){e.forEach(((e,t)=>{if(!e.role||!e.parts)throw new Error((0,o.__)("The content object must have a role and parts properties.","ai-services"));if(0===t&&"user"!==e.role)throw new Error((0,o.__)("The first content object in the history must be user content.","ai-services"));if(0===e.parts.length)throw new Error((0,o.__)("Each Content instance must have at least one part.","ai-services"))}))}(t),this.history=t):this.history=[]}getHistory(){return this.history}async sendMessage(e){const t=c(e),r=[...this.history,t],s=(await this.service.generateText(r,this.modelParams))[0].content;return this.history=[...this.history,t,s],s}}const p={};function v(e){return p[e.slug]||("browser"===e.slug?p[e.slug]=new u(e):p[e.slug]=new h(e)),p[e.slug]}const g="RECEIVE_SERVICES";function f(e,t){const r=t?.slugs||Object.keys(e);for(const s of r)if(e[s]&&e[s].is_available){if(t?.capabilities&&t.capabilities.filter((t=>!e[s].capabilities.includes(t))).length)continue;return s}return""}const y={services:void 0},w={receiveServices:e=>({dispatch:t})=>{t({type:g,payload:{services:e}})}},m={getServices:()=>async({dispatch:e})=>{const t=await a()({path:"/ai-services/v1/services"});t.push(await async function(){if(!n){const e=window.ai?await async function(e){const t=[];if(e.assistant){const r=await e.assistant.capabilities();r&&"readily"===r.available&&t.push("text_generation")}return t}(window.ai):[];n={slug:"browser",name:(0,o.__)("Browser built-in AI","ai-services"),is_available:e.length>0,capabilities:e,available_models:e.length>0?{default:e}:{}}}return n}()),e.receiveServices(t)}},b={getServices:e=>e.services,isServiceRegistered:(0,r.createRegistrySelector)((e=>(t,r)=>{const i=e(s).getServices();if(void 0!==i)return void 0!==i[r]})),isServiceAvailable:(0,r.createRegistrySelector)((e=>(t,r)=>{const i=e(s).getServices();if(void 0!==i)return void 0!==i[r]&&i[r].is_available})),hasAvailableServices:(0,r.createRegistrySelector)((e=>(t,r)=>{const i=e(s).getServices();if(void 0!==i)return!!f(i,r)})),getAvailableService:(0,r.createRegistrySelector)((e=>(t,r)=>{const i=e(s).getServices();if(void 0===i)return;if("string"==typeof r){const e=r;return i[e]&&i[e].is_available?v(i[e]):null}const a=f(i,r);return a?v(i[a]):null}))},_={initialState:y,actions:w,reducer:function(e=y,t){if(t.type===g){const{services:r}=t.payload;return{...e,services:r.reduce(((e,t)=>(e[t.slug]=t,e)),{})}}return e},resolvers:m,selectors:b},S="RECEIVE_CHAT",E="RECEIVE_CONTENT",C="REVERT_CONTENT",T="LOAD_CHAT_START",I="LOAD_CHAT_FINISH",A={capabilities:["text_generation"]},x={},j={chatConfigs:{},chatHistories:{},chatsLoading:{}};function H(...e){const t=e.reduce(((e,t)=>({...e,...t})),{}),r=function(e){const t=[],r={};for(let s=0;s<e.length;s++){const i=e[s];r[i]=r[i]>=1?r[i]+1:1,r[i]>1&&t.push(i)}return t}(e.reduce(((e,t)=>[...e,...Object.keys(t)]),[]));if(r.length)throw new Error(`collect() cannot accept collections with duplicate keys. Your call to collect() contains the following duplicated functions: ${r.join(", ")}. Check your data stores for duplicates.`);return t}const P=H,O=H,R=H,L=H,M=H;function N(...e){const t=[...e];let r;return"function"!=typeof t[0]&&(r=t.shift()),(e=r,s={})=>t.reduce(((e,t)=>t(e,s)),e)}function k(e){return e}const V=function(...e){const t=M(...e.map((e=>e.initialState||{})));return{initialState:t,controls:O(...e.map((e=>e.controls||{}))),actions:P(...e.map((e=>e.actions||{}))),reducer:N(t,...e.map((e=>e.reducer||k))),resolvers:R(...e.map((e=>e.resolvers||{}))),selectors:L(...e.map((e=>e.selectors||{})))}}(_,{initialState:j,actions:{startChat:(e,{service:t,modelParams:i})=>async({dispatch:a,select:o})=>{if(void 0===o.getServices()&&await(0,r.resolveSelect)(s).getServices(),t&&!o.isServiceAvailable(t))return void console.error(`The AI service ${t} is not available.`);if(!t&&!o.hasAvailableServices(A))return void console.error("No AI service available for text generation.");await a({type:T,payload:{chatId:e}});const n=o.getAvailableService(t||A),c=[],l=await n.startChat(c,i);a.receiveChat(e,{session:l,service:t,history:c,modelParams:i}),await a({type:I,payload:{chatId:e}})},sendMessage:(e,t)=>async({dispatch:r})=>{const s=x[e];if(!s)return void console.error(`Chat ${e} not found.`);const i=c(t);let a;r.receiveContent(e,i),await r({type:T,payload:{chatId:e}});try{a=await s.sendMessage(i)}catch(e){console.error(e?.message||e)}return a?r.receiveContent(e,a):r.revertContent(e),await r({type:I,payload:{chatId:e}}),a},receiveChat:(e,{session:t,service:r,history:s,modelParams:i})=>({type:S,payload:{chatId:e,session:t,service:r,history:s,modelParams:i}}),receiveContent:(e,t)=>({type:E,payload:{chatId:e,content:t}}),revertContent:e=>({type:C,payload:{chatId:e}})},reducer:function(e=j,t){switch(t.type){case S:{const{chatId:r,session:s,service:i,history:a,modelParams:o}=t.payload;return x[r]=s,{...e,chatConfigs:{...e.chatConfigs,[r]:{service:i,modelParams:o}},chatHistories:{...e.chatHistories,[r]:a},chatsLoading:{...e.chatsLoading,[r]:!1}}}case E:{const{chatId:r,content:s}=t.payload;return{...e,chatHistories:{...e.chatHistories,[r]:[...e.chatHistories[r],s]}}}case C:{const{chatId:r}=t.payload,s=e.chatHistories[r];return!s||s.length<1?e:{...e,chatHistories:{...e.chatHistories,[r]:1===s.length?[]:s.slice(0,-1)}}}case T:{const{chatId:r}=t.payload;return{...e,chatsLoading:{...e.chatsLoading,[r]:!0}}}case I:{const{chatId:r}=t.payload;return{...e,chatsLoading:{...e.chatsLoading,[r]:!1}}}}return e},resolvers:{},selectors:{getChat:(e,t)=>e.chatHistories[t]?e.chatHistories[t]:null,getChatConfig:(e,t)=>e.chatConfigs[t]?e.chatConfigs[t]:null,isChatLoading:(e,t)=>e.chatsLoading[t]}}),$=(0,r.createReduxStore)(s,V);(0,r.register)($),(window.aiServices=window.aiServices||{}).aiStore=t})();