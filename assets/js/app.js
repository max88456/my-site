function toggleSidebar(){
    var sidebar = document.getElementById('sidebar');
    if (!sidebar) return;
    if (window.innerWidth <= 1050) {
        sidebar.classList.toggle('open');
    } else {
        document.body.classList.toggle('sidebar-collapsed');
        localStorage.setItem('studtest-sidebar-collapsed', document.body.classList.contains('sidebar-collapsed') ? '1' : '0');
    }
}
if (localStorage.getItem('studtest-sidebar-collapsed') === '1') document.body.classList.add('sidebar-collapsed');
function toggleTheme(){const html=document.documentElement;const next=html.dataset.theme==='dark'?'light':'dark';html.dataset.theme=next;localStorage.setItem('studtest-theme',next)}
document.documentElement.dataset.theme=localStorage.getItem('studtest-theme')||'light';
document.addEventListener('click',e=>{if(e.target.matches('[data-confirm]')&&!confirm(e.target.dataset.confirm))e.preventDefault();});
document.querySelectorAll('.progress span').forEach(el=>{const w=el.dataset.width||el.style.width||'0%';el.style.width='0%';setTimeout(()=>el.style.width=w,120)});
function startTimer(seconds, formId){let left=seconds;const box=document.getElementById('timer');const form=document.getElementById(formId);if(!box)return;const tick=()=>{const m=Math.floor(left/60),s=left%60;box.textContent=`⏱ ${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}`;if(left<=0){if(form)form.submit();return;}left--;setTimeout(tick,1000)};tick();}
function addOption(){const wrap=document.getElementById('optionsWrap');if(!wrap)return;const i=wrap.children.length+1;const div=document.createElement('div');div.className='grid grid-2';div.innerHTML=`<input name="options[]" placeholder="Вариант ответа ${i}"><label class="option"><input type="checkbox" name="correct[]" value="${i-1}"> Правильный</label>`;wrap.appendChild(div)}

function confirmAction(msg){return confirm(msg||'Вы уверены?')}


// === StudTest visual charts without external libraries ===
function cssVar(name){return getComputedStyle(document.documentElement).getPropertyValue(name).trim() || '#2563eb'}
function parseDataAttr(el,name,fallback){try{return JSON.parse(el.dataset[name]||'')}catch(e){return fallback}}
function resizeCanvas(canvas){const dpr=window.devicePixelRatio||1;const rect=canvas.getBoundingClientRect();canvas.width=Math.max(1,Math.floor(rect.width*dpr));canvas.height=Math.max(1,Math.floor(rect.height*dpr));const ctx=canvas.getContext('2d');ctx.setTransform(dpr,0,0,dpr,0,0);return {ctx,w:rect.width,h:rect.height}}
function drawStudChart(canvas){const {ctx,w,h}=resizeCanvas(canvas);const type=canvas.dataset.type||'bar';const labels=parseDataAttr(canvas,'labels',[]);const values=parseDataAttr(canvas,'values',[]).map(Number);const values2=parseDataAttr(canvas,'values2',[]).map(Number);const max=Math.max(10,...values,...values2);const text=cssVar('--text'), muted=cssVar('--muted'), primary=cssVar('--primary'), accent=cssVar('--accent'), primary2=cssVar('--primary2'), line='rgba(148,163,184,.25)';ctx.clearRect(0,0,w,h);ctx.font='12px Inter, system-ui';ctx.lineWidth=1;const pad={l:42,r:18,t:18,b:42};
function grid(){ctx.strokeStyle=line;ctx.fillStyle=muted;for(let i=0;i<=4;i++){const y=pad.t+(h-pad.t-pad.b)*i/4;ctx.beginPath();ctx.moveTo(pad.l,y);ctx.lineTo(w-pad.r,y);ctx.stroke();const v=Math.round(max*(1-i/4));ctx.fillText(v+(canvas.dataset.suffix||''),6,y+4)}}
if(type==='donut'){const total=values.reduce((a,b)=>a+b,0)||1;let start=-Math.PI/2;const cx=w/2,cy=h/2,r=Math.min(w,h)*.34;const colors=[primary,accent,primary2,'#16a34a','#f59e0b','#ef4444'];values.forEach((v,i)=>{const a=v/total*Math.PI*2;ctx.beginPath();ctx.arc(cx,cy,r,start,start+a);ctx.lineWidth=28;ctx.strokeStyle=colors[i%colors.length];ctx.stroke();start+=a});ctx.fillStyle=text;ctx.font='900 28px Inter';ctx.textAlign='center';ctx.fillText((canvas.dataset.center||Math.round(total)),cx,cy+6);ctx.font='12px Inter';ctx.fillStyle=muted;ctx.fillText(canvas.dataset.centerLabel||'Всего',cx,cy+28);ctx.textAlign='left';return}
if(type==='line'){grid();const plotW=w-pad.l-pad.r, plotH=h-pad.t-pad.b;function line(vals,color){ctx.beginPath();vals.forEach((v,i)=>{const x=pad.l+(plotW*(i/(Math.max(1,vals.length-1))));const y=pad.t+plotH-(v/max)*plotH;if(i===0)ctx.moveTo(x,y);else ctx.lineTo(x,y)});ctx.strokeStyle=color;ctx.lineWidth=3;ctx.stroke();vals.forEach((v,i)=>{const x=pad.l+(plotW*(i/(Math.max(1,vals.length-1))));const y=pad.t+plotH-(v/max)*plotH;ctx.beginPath();ctx.arc(x,y,4,0,Math.PI*2);ctx.fillStyle=color;ctx.fill()})}line(values,primary);if(values2.length)line(values2,accent);ctx.fillStyle=muted;ctx.font='12px Inter';labels.forEach((lb,i)=>{if(labels.length>8 && i%2) return;const x=pad.l+(plotW*(i/(Math.max(1,labels.length-1))));ctx.fillText(String(lb).slice(0,12),x-14,h-16)});return}
if(type==='bar'){grid();const plotW=w-pad.l-pad.r, plotH=h-pad.t-pad.b;const gap=12;const bw=Math.max(14,(plotW-gap*(values.length-1))/Math.max(1,values.length));values.forEach((v,i)=>{const x=pad.l+i*(bw+gap);const bh=(v/max)*plotH;const y=pad.t+plotH-bh;const grd=ctx.createLinearGradient(0,y,0,y+bh);grd.addColorStop(0,primary);grd.addColorStop(1,accent);ctx.fillStyle=grd;roundRect(ctx,x,y,bw,bh,10);ctx.fill();ctx.fillStyle=muted;ctx.font='12px Inter';ctx.save();ctx.translate(x+bw/2,h-18);ctx.rotate(labels.length>5?-0.35:0);ctx.textAlign='center';ctx.fillText(String(labels[i]||'').slice(0,14),0,0);ctx.restore()});return}}
function roundRect(ctx,x,y,w,h,r){r=Math.min(r,w/2,h/2);ctx.beginPath();ctx.moveTo(x+r,y);ctx.arcTo(x+w,y,x+w,y+h,r);ctx.arcTo(x+w,y+h,x,y+h,r);ctx.arcTo(x,y+h,x,y,r);ctx.arcTo(x,y,x+w,y,r);ctx.closePath()}
function initStudCharts(){document.querySelectorAll('canvas.stud-chart').forEach(drawStudChart);document.querySelectorAll('[data-ring]').forEach(el=>el.style.setProperty('--p',el.dataset.ring||0));}
window.addEventListener('load',initStudCharts);window.addEventListener('resize',()=>{clearTimeout(window.__chartResize);window.__chartResize=setTimeout(initStudCharts,150)});
