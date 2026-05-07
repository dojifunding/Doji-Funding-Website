<?php
/**
 * Pixel Art Icon Sprite — Doji Funding
 * Source: Pixel icons pack (24×24, fill-rule evenodd, no stroke)
 */

function pix(string $id, int $w = 16, int $h = 16, string $cls = ''): string {
    $c = $cls ? ' class="' . htmlspecialchars($cls, ENT_QUOTES) . '"' : '';
    return '<svg width="' . $w . '" height="' . $h . '" viewBox="0 0 24 24" fill="currentColor" shape-rendering="crispEdges" aria-hidden="true"' . $c . '><use href="#px-' . $id . '"/></svg>';
}
?>
<svg xmlns="http://www.w3.org/2000/svg" style="display:none" aria-hidden="true">
  <defs>

    <symbol id="px-grid" viewBox="0 0 24 24">
      <path fill-rule="evenodd" clip-rule="evenodd" d="M2 2H22V22H2V2ZM4 4V8H8V4H4ZM10 4V8H14V4H10ZM16 4V8H20V4H16ZM20 10H16V14H20V10ZM20 16H16V20H20V16ZM14 20V16H10V20H14ZM8 20V16H4V20H8ZM4 14H8V10H4V14ZM10 10V14H14V10H10Z" fill="currentColor"/>
    </symbol>

    <symbol id="px-layers" viewBox="0 0 24 24">
      <path fill-rule="evenodd" clip-rule="evenodd" d="M2 5H22V19H2V5ZM4 7V11H20V7H4ZM20 13H10V17H20V13ZM8 17V13H4V17H8Z" fill="currentColor"/>
    </symbol>

    <symbol id="px-gear" viewBox="0 0 24 24">
      <path fill-rule="evenodd" clip-rule="evenodd" d="M17 4H19V14H17V4ZM17 16H15V18H17V20H19V18H21V16H19H17ZM13 10H11V20H13V10ZM5 12H3V14H5V20H7V14H9V12H7H5ZM13 4H11V6H9V8H11H13H15V6H13V4ZM5 4H7V10H5V4Z" fill="currentColor"/>
    </symbol>

    <symbol id="px-wallet" viewBox="0 0 24 24">
      <path fill-rule="evenodd" clip-rule="evenodd" d="M18 3H4H2V5V19V21H4H18H20V19V17H22V15V9V7H20V5V3H18ZM18 17V19H4V5H18V7H12H10V9V15V17H12H18ZM20 15H18H12V9H18H20V15ZM16 11H14V13H16V11Z" fill="currentColor"/>
    </symbol>

    <symbol id="px-dollar" viewBox="0 0 24 24">
      <path fill-rule="evenodd" clip-rule="evenodd" d="M10.9998 2H12.9998V6H19V8H7.00003V11H5.00003V8H5V6H10.9998V2ZM5 18H11V22H13V18H19V16H5V18ZM19 11H5V13H17V16H19V13H19V11Z" fill="currentColor"/>
    </symbol>

    <symbol id="px-bar-chart" viewBox="0 0 24 24">
      <path fill-rule="evenodd" clip-rule="evenodd" d="M13 5H15V19H13V5ZM11 9H9V19H11V9ZM7 13H5V19H7V13ZM19 13H17V19H19V13Z" fill="currentColor"/>
    </symbol>

    <symbol id="px-trophy" viewBox="0 0 24 24">
      <path fill-rule="evenodd" clip-rule="evenodd" d="M16.0002 3H8H6V5L4.00012 5H2.00012V7V15H4.00012L6 15H8V7H8.00012V5L16.0002 5V7V15L18.0001 15L18.0002 15L20 15H22V7H22.0002V5H22H20H18.0002V3H18H16.0002ZM20 7V13H18.0002V7H20ZM6 13H4.00012V7H6V13ZM18 15.0001H6V17.0001H18V15.0001ZM11 17.0001H13V19H16.0002V21H13V21.0001H11V21H8.00024V19H11V17.0001Z" fill="currentColor"/>
    </symbol>

    <symbol id="px-columns" viewBox="0 0 24 24">
      <path fill-rule="evenodd" clip-rule="evenodd" d="M2 5H22V19H2V5ZM4 7V17H11V7H4ZM13 7V17H20V7H13Z" fill="currentColor"/>
    </symbol>

    <symbol id="px-medal" viewBox="0 0 24 24">
      <path fill-rule="evenodd" clip-rule="evenodd" d="M9 2H2V4H7V8H2V10H9V8V7H14V17H9V16V14H2V16H7V20H2V22H9V20V19H16V17V13H22V11H16V7V5H9V4V2Z" fill="currentColor"/>
    </symbol>

    <symbol id="px-calendar" viewBox="0 0 24 24">
      <path fill-rule="evenodd" clip-rule="evenodd" d="M15 2H17V4H19H21V8V10V22H19H5H3V10V8V4H5H7V2H9V4H15V2ZM5 8H19V6H5V8ZM5 10V20H19V10H5Z" fill="currentColor"/>
    </symbol>

    <symbol id="px-share" viewBox="0 0 24 24">
      <path fill-rule="evenodd" clip-rule="evenodd" d="M14 5H12V9H6V11H4V17H6V15H12V19H14V17H16V15H18V13H20V11H18V9H16V7H14V5Z" fill="currentColor"/>
    </symbol>

    <symbol id="px-star" viewBox="0 0 24 24">
      <path fill-rule="evenodd" clip-rule="evenodd" d="M18 2H6V4H17.9995V20H15.9998V18H13.9998V16H9.99976L9.99976 18H7.99976V20H5.99976V2.00012H3.99976V22.0001H5.99976V22H7.99976V20L9.99976 20V18H13.9998V20L15.9998 20V22H17.9995V22.0001H19.9995V2.00012H18V2Z" fill="currentColor"/>
    </symbol>

    <symbol id="px-question" viewBox="0 0 24 24">
      <path d="M8 4h8v2H8V4ZM6 6h2v2H6V6ZM16 6h2v4H16V6ZM14 10h2v2H14V10ZM12 12h2v2H12V12ZM10 14h4v2H10V14ZM10 18h4v2H10V18Z" fill="currentColor"/>
    </symbol>

    <symbol id="px-user" viewBox="0 0 24 24">
      <path fill-rule="evenodd" clip-rule="evenodd" d="M15 2H9V3.99994H7.00024V9.99994H9.00024V4H15V2ZM15 10H9V12H15V10ZM15.0002 3.99994H17.0002V9.99994H15.0002V3.99994ZM4 15.9999H6V14H18V16H6V20H18.0002V15.9999H20.0002V21.9999H20V22H4V21.9999V20V15.9999Z" fill="currentColor"/>
    </symbol>

    <symbol id="px-chevron-down" viewBox="0 0 24 24">
      <path fill-rule="evenodd" clip-rule="evenodd" d="M7 8H5V10H7V12H9V14H11V16H13V14H15V12H17V10H19V8H17V10H15V12H13V14H11V12H9V10H7V8Z" fill="currentColor"/>
    </symbol>

    <symbol id="px-chevron-right" viewBox="0 0 24 24">
      <path fill-rule="evenodd" clip-rule="evenodd" d="M8 5L8 7L10 7L10 5L8 5ZM12 9L12 7L10 7L10 9L12 9ZM14 11L14 9L12 9L12 11L14 11ZM14 13L16 13L16 11L14 11L14 13ZM12 15L12 13L14 13L14 15L12 15ZM12 15L10 15L10 17L12 17L12 15ZM8 19L8 17L10 17L10 19L8 19Z" fill="currentColor"/>
    </symbol>

    <symbol id="px-bell" viewBox="0 0 24 24">
      <path fill-rule="evenodd" clip-rule="evenodd" d="M14.0001 4V2H10.0001V4H5.00024V6H19.0002V4H14.0001ZM19 16H5V12H3V16V18L7.99981 18V22H9.99981V18H13.9999V20H10.0001V22H13.9999V22H15.9999V18L21 18V16L21 12H19.0001V6H17.0001V14H19V16ZM5.00024 6V14H7.00024V6H5.00024Z" fill="currentColor"/>
    </symbol>

    <symbol id="px-globe" viewBox="0 0 24 24">
      <path fill-rule="evenodd" clip-rule="evenodd" d="M8 2H10V4H12V6H10V16H8V6H6V4H8V2ZM4 8V6H6V8H4ZM6 18V20H4V22H2V8H4V18H6ZM6 18H8V16H6V18ZM12 18H10V16H12V18ZM14 8V6H12V8H14ZM16 8H14V18H12V20H14V22H16V20H18V18H20V16H22V4V2H20V4H18V6H16V8ZM16 8H18V6H20V16H18V18H16V8Z" fill="currentColor"/>
    </symbol>

    <symbol id="px-logout" viewBox="0 0 24 24">
      <path fill-rule="evenodd" clip-rule="evenodd" d="M5 3H19H21V5V7H19V5H5V19H19V17H21V19V21H19H5H3V19V5V3H5ZM21 11H19V9H17V7H15V9H17V11H7V13L17 13V15H15V17H17V15H19V13L21 13V11Z" fill="currentColor"/>
    </symbol>

    <symbol id="px-menu" viewBox="0 0 24 24">
      <path fill-rule="evenodd" clip-rule="evenodd" d="M4 6H20V8H4V6ZM4 11.0001H20V13.0001H4V11.0001ZM20 16H4V18H20V16Z" fill="currentColor"/>
    </symbol>

    <symbol id="px-external" viewBox="0 0 24 24">
      <path fill-rule="evenodd" clip-rule="evenodd" d="M21 11V5V3H19H13V5H17V7H15V9H13V11H11V13H9V15H11V13H13V11H15V9H17V7H19V11H21ZM11 5H5H3V7V19V21H5H17H19V19V13H17V19H5V7H11V5Z" fill="currentColor"/>
    </symbol>

    <symbol id="px-plus" viewBox="0 0 24 24">
      <path fill-rule="evenodd" clip-rule="evenodd" d="M11 4H13V6V11H20V13H13V18V20H11V18V13H4V11H11V6V4Z" fill="currentColor"/>
    </symbol>

    <symbol id="px-arrow-ur" viewBox="0 0 24 24">
      <path fill-rule="evenodd" clip-rule="evenodd" d="M6 8H16V6H18V8H20V10H18V12H16V10H6V20H4V10V8H6ZM16 12V14H14V12H16ZM16 6V4H14V6H16Z" fill="currentColor"/>
    </symbol>

    <symbol id="px-copy" viewBox="0 0 24 24">
      <path fill-rule="evenodd" clip-rule="evenodd" d="M4 2H15V4H6V17H4V2ZM8 6H20V22H8V6ZM10 8V20H18V8H10Z" fill="currentColor"/>
    </symbol>

    <symbol id="px-eye" viewBox="0 0 24 24">
      <path fill-rule="evenodd" clip-rule="evenodd" d="M8 6H16V8H8V6ZM4 10V8H8V10H4ZM2 12V10H4V12H2ZM2 14V12H0V14H2ZM4 16H2V14H4V16ZM8 18H4V16H8V18ZM16 18V20H8V18H16ZM20 16V18H16V16H20ZM22 14V16H20V14H22ZM22 12H24V14H22V12ZM20 10H22V12H20V10ZM20 10V8H16V10H20ZM10 11H14V15H10V11Z" fill="currentColor"/>
    </symbol>

    <symbol id="px-refresh" viewBox="0 0 24 24">
      <path fill-rule="evenodd" clip-rule="evenodd" d="M16 2H14V4H16V6H4V8H2V13H4V8H16V10H14V12H16V10H18V8H20V6H18V4H16V2ZM6 20H8V22H10V20H8V18H20V16H22V11H20V16H8V14H10V12H8V14H6V16H4V18H6V20Z" fill="currentColor"/>
    </symbol>

    <symbol id="px-trash" viewBox="0 0 24 24">
      <path fill-rule="evenodd" clip-rule="evenodd" d="M15.9995 2H15.9998V4H15.9995V6H17.9995H19.9995H21.9995V8H19.9995V20H19.9998V22L19.9995 22H17.9995H5.99976L3.99976 22V20V8H1.99951V6H3.99976H5.99976H7.99976V4V2H9.99976H13.9995H15.9995ZM13.9995 4H9.99976V6H13.9995V4ZM13.9995 8H9.99976H7.99976L5.99976 8V20H17.9995V8L15.9995 8H13.9995ZM8.99951 10H10.9995V18H8.99951V10ZM14.9998 10H12.9998V18H14.9998V10Z" fill="currentColor"/>
    </symbol>

    <symbol id="px-check" viewBox="0 0 24 24">
      <path fill-rule="evenodd" clip-rule="evenodd" d="M18 6H20V8H18V6ZM16 10V8H18V10H16ZM14 12V10H16V12H14ZM12 14H14V12H12V14ZM10 16H12V14H10V16ZM8 16V18H10V16H8ZM6 14H8V16H6V14ZM6 14H4V12H6V14Z" fill="currentColor"/>
    </symbol>

    <symbol id="px-close" viewBox="0 0 24 24">
      <path fill-rule="evenodd" clip-rule="evenodd" d="M5 5H7V7H5V5ZM9 9H7V7H9V9ZM11 11H9V9H11V11ZM13 11H11V13H9V15H7V17H5V19H7V17H9V15H11V13H13V15H15V17H17V19H19V17H17V15H15V13H13V11ZM15 9V11H13V9H15ZM17 7V9H15V7H17ZM17 7V5H19V7H17Z" fill="currentColor"/>
    </symbol>

    <symbol id="px-lock" viewBox="0 0 24 24">
      <path fill-rule="evenodd" clip-rule="evenodd" d="M15 2H9V4H7V8H6H4V10V20V22H6H18H20V20V10V8H18H17V4H15V2ZM15 4V8H9V4H15ZM9 10H15H17H18V20H6V10H7H9ZM13 13H11V17H13V13Z" fill="currentColor"/>
    </symbol>

    <symbol id="px-info" viewBox="0 0 24 24">
      <path fill-rule="evenodd" clip-rule="evenodd" d="M2.99951 3H4.99951V21H2.99951V3ZM18.9998 3.00003H5V5.00003H18.9998V19H5V21H19V21H20.9998V3H18.9998V3.00003ZM10.9998 9.00009H12.9998V7.00009H10.9998V9.00009ZM12.9998 17H10.9998V11H12.9998V17Z" fill="currentColor"/>
    </symbol>

    <symbol id="px-shield" viewBox="0 0 24 24">
      <path fill-rule="evenodd" clip-rule="evenodd" d="M22 2H21.9995H19.9995H4H2V4V14H4V4H19.9995V14H21.9995V4H22V2ZM5.99976 14.0001H3.99976V16.0001H5.99976V14.0001ZM6 16.0001H8V18H10V20H8V18.0001H6V16.0001ZM10 20V22H14V20H15.9995V18H13.9995V20H10ZM19.9995 14.0001H17.9995V16.0001H16V18.0001H18V16.0001H19.9995V14.0001Z" fill="currentColor"/>
    </symbol>

    <symbol id="px-link" viewBox="0 0 24 24">
      <path fill-rule="evenodd" clip-rule="evenodd" d="M4 6H11V8H4V16H11V18H4H2V16V8V6H4ZM20 6H13V8H20V16H13V18H20H22V16V8V6H20ZM17 11H7V13H17V11Z" fill="currentColor"/>
    </symbol>

    <symbol id="px-download" viewBox="0 0 24 24">
      <path fill-rule="evenodd" clip-rule="evenodd" d="M12.9998 17L12.9998 3H10.9998L10.9998 13H9V11H7V13H8.99988V15H10.9998V17H12.9998ZM20.9998 18.9999V15H18.9998V18.9999L4.99976 18.9999L4.99976 15H2.99976V18.9999V20.9999V21L4.99976 21V20.9999L18.9998 20.9999V21L20.9998 21V20.9999V18.9999ZM12.9999 12.9999V14.9999H14.9999V12.9999H16.9999V10.9999H14.9999V12.9999H12.9999Z" fill="currentColor"/>
    </symbol>

    <symbol id="px-upload" viewBox="0 0 24 24">
      <path fill-rule="evenodd" clip-rule="evenodd" d="M11 4.99994V3H13V4.99994L15.0001 4.99994V6.99994L17 6.99994V8.99994H15V6.99994L13 6.99994V17H11L11 6.99994H9.00012V8.99994H7.00012V6.99994H9.00009V4.99994L11 4.99994ZM3 15V18.9999V20.9999V21L5 21V20.9999L19 20.9999V21L21 21L21 20.9999V18.9999L21 15H19V18.9999L5 18.9999L5 15H3Z" fill="currentColor"/>
    </symbol>

    <symbol id="px-file" viewBox="0 0 24 24">
      <path fill-rule="evenodd" clip-rule="evenodd" d="M3 22H21V8H19V6H17V8H15V6H17V4H15V2H3V22ZM5 20V4H13V10H19V20H5Z" fill="currentColor"/>
    </symbol>

    <symbol id="px-users" viewBox="0 0 24 24">
      <path fill-rule="evenodd" clip-rule="evenodd" d="M11 2H5V4H3.00024V10L5 10V12H11V10L5.00024 10V4H11V2ZM11.0002 4H13.0002V10H11.0002V4ZM0 16H2V20H14V22H2H0V16ZM2 16H14V14H2V16ZM16.0002 16H14.0002V22H16.0002V16ZM15 2H19V4H15V2ZM19 10H15V12H19V10ZM19.0002 4H21.0002V10H19.0002V4ZM24.0002 16H22.0002V20H18V22H24V20H24.0002V16ZM18 14H22V16H18V14Z" fill="currentColor"/>
    </symbol>

    <symbol id="px-message" viewBox="0 0 24 24">
      <path fill-rule="evenodd" clip-rule="evenodd" d="M20 2H4H2H2V4H2V22H4V4H20V16H6V18H4.00025V20H6.00025V18H20H22V16V4V2H20Z" fill="currentColor"/>
    </symbol>

    <symbol id="px-credit-card" viewBox="0 0 24 24">
      <path fill-rule="evenodd" clip-rule="evenodd" d="M4 4H20V6H4V8H20V12H4V18H20V20H4L4 20H2V4.00002H4L4 4ZM22.0002 4.00002H20.0002V20H22.0002V4.00002Z" fill="currentColor"/>
    </symbol>

    <symbol id="px-briefcase" viewBox="0 0 24 24">
      <path fill-rule="evenodd" clip-rule="evenodd" d="M8 3H16V7H22V21H2V7H8V3ZM10 7H14V5H10V7ZM4 9V19H20V9H4Z" fill="currentColor"/>
    </symbol>

    <symbol id="px-tag" viewBox="0 0 24 24">
      <path fill-rule="evenodd" clip-rule="evenodd" d="M12 2H4H2V4V12H4V14H6V16H8V18H10V20H12V22H14V20H16V18H18V16H20V14H22V12H20V10H18V8H16V6H14V4H12V2ZM12 4V6H14V8H16V10H18V12H20V14H18V16H16V18H14V20H12V18H10V16H8V14H6V12H4V4H12ZM6 6H8V8H6V6Z" fill="currentColor"/>
    </symbol>

    <symbol id="px-pen" viewBox="0 0 24 24">
      <path fill-rule="evenodd" clip-rule="evenodd" d="M18 2H16V4H14V6H12V8H10V10H8V12H6V14H4V16H2V20V22H4H8V20H10V18H12V16H14V14H16V12H18V10H20V8H22V6H20V4H18V2ZM18 10H16V12H14V14H12V16H10V18H8V16H6V14H8V12H10V10H12V8H14V6H16V8H18V10ZM6 16H4V20H8V18H6V16Z" fill="currentColor"/>
    </symbol>

    <symbol id="px-table" viewBox="0 0 24 24">
      <path fill-rule="evenodd" clip-rule="evenodd" d="M2 3H22V21H2V3ZM4 7V12H11V7H4ZM13 7V12H20V7H13ZM20 14H13V19H20V14ZM11 19V14H4V19H11Z" fill="currentColor"/>
    </symbol>

    <symbol id="px-monitor" viewBox="0 0 24 24">
      <path fill-rule="evenodd" clip-rule="evenodd" d="M20.0002 3H4H2V5V14.9999V16.9999V17H4V16.9999H10V18.9999H8V20.9999H10H14H16V18.9999H14V16.9999H20.0002V17H22.0002V3H22H20.0002ZM14 14.9999H10H4V5H20.0002V14.9999H14Z" fill="currentColor"/>
    </symbol>

    <symbol id="px-lightning" viewBox="0 0 24 24">
      <path fill-rule="evenodd" clip-rule="evenodd" d="M12 1H14V9H22V11V13H20V11H14H12V9V5H10V3H12V1ZM8 7V5H10V7H8ZM6 9V7H8V9H6ZM4 11V9H6V11H4ZM14 19V21H12V23H10V15H2V13V11H4V13H10H12V15V19H14ZM16 17V19H14V17H16ZM18 15V17H16V15H18ZM18 15H20V13H18V15Z" fill="currentColor"/>
    </symbol>

    <symbol id="px-pulse" viewBox="0 0 24 24">
      <path fill-rule="evenodd" clip-rule="evenodd" d="M3 3H5H19H21V21H19H5H3V3ZM19 5H5V19H19V5ZM7 12H9V17H7V12ZM17 7H15V17H17V7ZM11 10H13V12H11V10ZM13 14H11V17H13V14Z" fill="currentColor"/>
    </symbol>

    <symbol id="px-more" viewBox="0 0 24 24">
      <path fill-rule="evenodd" clip-rule="evenodd" d="M1 9H7V15H1V9ZM3 11V13H5V11H3ZM9 9H15V15H9V9ZM11 11V13H13V11H11ZM17 9H23V15H17V9ZM19 11V13H21V11H19Z" fill="currentColor"/>
    </symbol>

    <symbol id="px-wifi" viewBox="0 0 24 24">
      <path fill-rule="evenodd" clip-rule="evenodd" d="M19 2H11V4H9V6H11V4H19V6H21V4H19V2ZM11 8H13V10H11V8ZM17 8V6H13V8H17ZM17 8H19V10H17V8ZM16 10H14V12H4H2V22H4H20H22V12H20H16V10ZM20 14V20H4V14H20ZM18 16H16V18H18V16ZM12 16H14V18H12V16Z" fill="currentColor"/>
    </symbol>

    <symbol id="px-coin" viewBox="0 0 24 24">
      <path fill-rule="evenodd" clip-rule="evenodd" d="M6 2H18V4H6V2ZM4 6V4H6V6H4ZM4 18V6H2V18H4ZM6 20V18H4V20H6ZM18 20V22H6V20H18ZM20 18V20H18V18H20ZM20 6H22V18H20V6ZM20 6V4H18V6H20ZM11 5H13V7H16V9H13H11H10V11H14H16V13V15V17H14H13V19H11V17H8V15H11H13H14V13H10H8V11V9V7H10H11V5Z" fill="currentColor"/>
    </symbol>

    <symbol id="px-clock" viewBox="0 0 24 24">
      <path fill-rule="evenodd" clip-rule="evenodd" d="M19 3H5V5H3V19H5V21H19V19H21V5H19V3ZM19 5V19H5V5H19ZM11 7H13V13H17V15L13 15H11V7Z" fill="currentColor"/>
    </symbol>

    <symbol id="px-heart" viewBox="0 0 24 24">
      <path d="M9 2H5V4H3V6H1V12H3V14H5V16H7V18H9V20H11V22H13V20H15V18H17V16H19V14H21V12H23V6H21V4H19V2H15V4H13V6H11V4H9V2Z" fill="currentColor"/>
    </symbol>

  </defs>
</svg>
