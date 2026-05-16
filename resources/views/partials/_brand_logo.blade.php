{{-- ITAMS / RRS brand mark glyph.
     White-on-gradient layered "asset stack" with a live-status LED — designed to sit
     inside a wrapper that supplies the gradient background (e.g. .brand-mark,
     .auth-brand-mark). Scales fluidly via viewBox + width/height 100%. --}}
<svg class="rrs-logo"
     viewBox="0 0 24 24"
     width="100%"
     height="100%"
     fill="none"
     xmlns="http://www.w3.org/2000/svg"
     aria-hidden="true"
     focusable="false">
    {{-- Stack of three tapered asset layers (top → bottom: most → least opaque) --}}
    <rect x="3.5" y="4"  width="17" height="4" rx="1.25" fill="white" opacity="0.97"/>
    <rect x="5"   y="10" width="14" height="4" rx="1.25" fill="white" opacity="0.78"/>
    <rect x="6.5" y="16" width="11" height="4" rx="1.25" fill="white" opacity="0.6"/>
    {{-- Glass highlight along the top edge of the lead bar --}}
    <rect x="3.5" y="4"  width="17" height="1" rx="1.25" fill="white" opacity="0.4"/>
    {{-- Live status LED (operational green) --}}
    <circle cx="6" cy="6" r="0.85" fill="#34d399"/>
</svg>
