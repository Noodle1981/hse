---
description: Ultra minización
---

Etapa 1

Para el perfil de gestor, necesito quitar las siguientes vistas

http://127.0.0.1:8000/gestor/epp/history

http://127.0.0.1:8000/gestor/epp/alerts

quitar las rutas, revisar si no se rompe algo, quitar todos los enlaces que haya en navbar lateral

Etapa 2 

Para el perfil de técnico, necesito quitar la siguiente vista

http://127.0.0.1:8000/tecnico/epp/deliver

quitar las rutas, revisar si no se rompe algo, quitar todos los enlaces que haya en navbar lateral


Etapa 3

borrar controladores, modelos referido a EP, entregas, alertas, stock, ect. incluyendo controladores de pdf referido a eso

Etapa 4

borrar los campos de borrar controladores, modelos referido a EP, entregas, alertas, stock, ect. DB


Etapa 5

renombrar la carpeta risk por contrato anual y revisar que no rompa el namespace 
revisar si el controlador AngularController.php es necesario

armar un roadmap para ello, llamado ultraminimizacion.md