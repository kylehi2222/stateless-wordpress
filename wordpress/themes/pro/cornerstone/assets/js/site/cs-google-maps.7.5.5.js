(()=>{var{attach:M,util:h}=window.csGlobal.rivet;var c=new WeakMap,k=r=>{if(!c.has(r)){let o={markers:new Set,active:!1};c.set(r,o)}return c.get(r)};window.csGoogleMapsLoad=function(){M("[data-x-element-map_google_marker]",(o,e)=>{let a=k(o.closest("[data-x-element-map-google]"));r(a,e)},10);function r(o,{lat:e,lng:a,imageSrc:i,imageWidth:n,imageOffsetX:s,imageHeight:l,imageOffsetY:w,imageRetina:b,content:d,contentStart:C}){let{markers:y}=o;y.add(p=>{let g={map:p,position:new window.google.maps.LatLng(parseFloat(e),parseFloat(a))};if(i){let t={url:i,origin:new window.google.maps.Point(0,0)},m=Math.abs(n/(100/parseFloat(s))-n/2),u=Math.abs(l/(100/parseFloat(w))-l/2);b?(t.anchor=new window.google.maps.Point(m/2,u/2),t.scaledSize=new window.google.maps.Size(n/2,l/2)):(t.anchor=new window.google.maps.Point(m,u),t.size=new window.google.maps.Size(n,l)),g.icon=t}let f=new window.google.maps.Marker(g);if(d!==""){let t=new window.google.maps.InfoWindow({content:d,maxWidth:200});C==="open"&&t.open(p,f),window.google.maps.event.addListener(f,"click",function(){t.open(p,this)})}})}M("[data-x-element-map-google]",(o,e={})=>{if(!window.google||!window.google.maps)return;let a=k(o),i=o.getAttribute("data-x-map-markers");if(i&&JSON.parse(i).forEach(function(w){!w||typeof w!="object"||r(a,w)}),a.active)return;a.active=!0;let n=new window.google.maps.LatLng(e.lat,e.lng),s=new window.google.maps.Map(o,{mapTypeId:"roadmap",center:n,draggable:e.drag,zoomControl:e.zoom,zoom:parseInt(e.zoomLevel,10),clickableIcons:!1,disableDefaultUI:!0,disableDoubleClickZoom:!1,fullscreenControl:!1,mapTypeControl:!1,rotateControl:!1,scrollwheel:!1,streetViewControl:!1,backgroundColor:"transparent"});s.mapTypes.set("map_google",new window.google.maps.StyledMapType(e.styles===""?null:JSON.parse(e.styles),{name:"Styled Map"})),s.setMapTypeId("map_google"),a.markers.forEach(l=>void l(s)),e.drag||s.addListener("center_changed",function(){s.panTo(n)})},100),window.csGoogleMapsClassic&&window.csGoogleMapsClassic()};})();
