
<template>
<div>

<nav class="menu">

<RouterLink to="/">Főoldal</RouterLink>
<RouterLink to="/weather">Időjárás</RouterLink>
<RouterLink to="/catches">Fogások</RouterLink>

</nav>

<RouterView/>

</div>
</template>

<style>
body{
margin:0;
font-family:Arial;
background: url('@/assets/2.jpg') center/cover no-repeat fixed;
color:white;
}

.menu{
display:flex;
gap:20px;
padding:20px;
background:rgba(0,0,0,0.6);
}

a{color:white;text-decoration:none}
</style>
