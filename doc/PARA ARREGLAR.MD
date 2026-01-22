# Organización por Roles

## ROL: GESTOR
### Sidebar que debe ver:
- Historial de Visitas: el Gestor solo ve las visitas de los técnicos
     - http://127.0.0.1:8000/visits/history > http://127.0.0.1:8000/gestor/visits/history 
- Checklist - Este rol crea los checklist, con categorias y sus categoría, permitiendolo personalizarlo y genera plantillas (dichas plantillas el tecnico las ocupa si se le asigna uno, o si el quiere tomar uno al azar cuando inicia una visita)
     - http://127.0.0.1:8000/checklists > http://127.0.0.1:8000/gestor/checklists 
     - http://127.0.0.1:8000/checklists/create > http://127.0.0.1:8000/gestor/checklists/create 
     - http://127.0.0.1:8000/checklists/1/edit > http://127.0.0.1:8000/gestor/checklists/1/edit 
     - http://127.0.0.1:8000/checklists/1 > http://127.0.0.1:8000/gestor/checklists/1 
     - http://127.0.0.1:8000/categories > http://127.0.0.1:8000/gestor/categories 
     - http://127.0.0.1:8000/categories/create > http://127.0.0.1:8000/gestor/categories/create 
     - http://127.0.0.1:8000/categories/1/edit > http://127.0.0.1:8000/gestor/categories/1/edit 
     - http://127.0.0.1:8000/categories/1/subcategories > http://127.0.0.1:8000/gestor/categories/1/subcategories 
     - http://127.0.0.1:8000/categories/1/subcategories/1/edit > http://127.0.0.1:8000/gestor/categories/1/subcategories/1/edit 
     - http://127.0.0.1:8000/categories/1/subcategories/1 > http://127.0.0.1:8000/gestor/categories/1/subcategories/1

    # Aclaración el operario llama a la plantilla de checklist para ocupar una que necesite, o completar una que les haya asignado (revisar permisos al final)
     
- Capacitaciones: El gestor puede crear, editar y eliminar capacitaciones, dichas capacitaciones las puede asignar a los técnicos, y ademas ve un historial de las capacitaciones dadas por los técnicos
    - http://127.0.0.1:8000/trainings > http://127.0.0.1:8000/gestor/trainings 
    - http://127.0.0.1:8000/trainings/create > http://127.0.0.1:8000/gestor/trainings/create 
    - http://127.0.0.1:8000/trainings/1/edit > http://127.0.0.1:8000/gestor/trainings/1/edit 
    - http://127.0.0.1:8000/trainings/1/delete > http://127.0.0.1:8000/gestor/trainings/1/delete 
    - http://127.0.0.1:8000/trainings/1/attendance > http://127.0.0.1:8000/gestor/trainings/1/attendance 
    - http://127.0.0.1:8000/trainings/1/pdf/preview > http://127.0.0.1:8000/gestor/trainings/1/pdf/preview 
    - http://127.0.0.1:8000/trainings/1/pdf > http://127.0.0.1:8000/gestor/trainings/1/pdf 
  
    # Aclaración el operario puede llamar generar su propia capacitación parra ocupar una que necesite, o completar una que les haya asignado (revisar permisos al final)
    
- Historial: el gestor puede ver historial de checklist, pero se va a refuncionalizar igual mantendremos esta ruta
    - http://127.0.0.1:8000/inspections > http://127.0.0.1:8000/gestor/inspections 
    - http://127.0.0.1:8000/inspections/1 > http://127.0.0.1:8000/gestor/inspections/1
    - http://127.0.0.1:8000/inspections/1/pdf/preview > http://127.0.0.1:8000/gestor/inspections/1/pdf/preview
    - http://127.0.0.1:8000/inspections/1/pdf > http://127.0.0.1:8000/gestor/inspections/1/pdf
    - la ruta de eleminar borrador actualizarla para que sea http://127.0.0.1:8000/gestor/inspections/1/delete



- Hallazgos esta es la vista de los hallazgos de los tecnicos

   - http://127.0.0.1:8000/hallazgos > http://127.0.0.1:8000/gestor/hallazgos


- Incidentes - esta vista es la de los incidentes de los tecnicos

   - http://127.0.0.1:8000/incidentes > http://127.0.0.1:8000/gestor/incidentes
   - 


- Gestión EPP

   -http://127.0.0.1:8000/epp/history > http://127.0.0.1:8000/gestor/epp/history
  
  el boton de + nueva entrega no debe estar en la vista de gestor



- Ejecuciones Avanzadas : la vista general de ejecuciones avanzadas 

  - http://127.0.0.1:8000/risk-matrices/ejecuciones-avanzadas > http://127.0.0.1:8000/gestor/risk-matrices/ejecuciones-avanzadas

   dentro de ella tenemos la vista Matrices de Riesgo, que solo la diseña el Gestor para que lo complete el tecnico

   - http://127.0.0.1:8000/risk-matrices > http://127.0.0.1:8000/gestor/risk-matrices
   - http://127.0.0.1:8000/risk-matrices/1/editor > http://127.0.0.1:8000/gestor/risk-matrices/1/editor
   - http://127.0.0.1:8000/risk-matrices/1/pdf/preview > http://127.0.0.1:8000/gestor/risk-matrices/1/pdf/preview
   - http://127.0.0.1:8000/risk-matrices/1/pdf > http://127.0.0.1:8000/gestor/risk-matrices/1/pdf
    
   Vista Contrato Anuales : aca la diseña el gestor para que complete el tecnico

   http://127.0.0.1:8000/risk-matrices/contrato-anual > http://127.0.0.1:8000/gestor/risk-matrices/contrato-anual
  
   en donde el gestor genera un plan anual con las distintas categorias de inspeccion, capacitacion, ect. (esta en proceso de desarrollo)


- Administración : exclusivo del gestor

    - http://127.0.0.1:8000/administracion > http://127.0.0.1:8000/gestor/administracion
    - http://127.0.0.1:8000/users > http://127.0.0.1:8000/gestor/users
    - http://127.0.0.1:8000/companies > http://127.0.0.1:8000/gestor/companies
    - http://127.0.0.1:8000/roles > http://127.0.0.1:8000/gestor/roles
    - http://127.0.0.1:8000/permissions > http://127.0.0.1:8000/gestor/permissions
    - http://127.0.0.1:8000/epp/employees > http://127.0.0.1:8000/gestor/epp/employees


- Dashboard - cada rol tendra su dashboard caracteristico 

   - http://127.0.0.1:8000/admin/dashboard > http://127.0.0.1:8000/gestor/dashboard
   -


## ROL: TÉCNICO
### Sidebar que debe ver:

- Mis Visitas son las visitas propias del gestor, en donde tomará ciertas cosas que genera el gestor, para analizar los permisos entre ellos

 - http://127.0.0.1:8000/visits/in-progress/4 > http://127.0.0.1:8000/tecnico/visits/in-progress/4

- Mis Tareas son las tareas pendiente que tiene que hacer (esta implementado checklists, pero deberia verse, pedido de capacitaciones, informes, etc, informes todavia no esta empleado)

- Historial (se pienza refuncionalizar, ya que mis visitas cumple con ello)
  -http://127.0.0.1:8000/inspections > http://127.0.0.1:8000/tecnico/inspections



- Gestión EPP solo es para entregar epp, se va a refuncionalizar, porque en la visita llama a generar entrega por una constancia, será como para visualizar las entregas

- http://127.0.0.1:8000/epp/history > http://127.0.0.1:8000/tecnico/epp/history

- Ejecuciones Avanzadas : seria la version del portal para completar las ejecuciones avanzadas

http://127.0.0.1:8000/risk-matrices/ejecuciones-avanzadas > http://127.0.0.1:8000/tecnico/risk-matrices/ejecuciones-avanzadas


Matrices de riesgos - acción evaluar riesgos

 - http://127.0.0.1:8000/risk-matrices > http://127.0.0.1:8000/tecnico/risk-matrices
 - http://127.0.0.1:8000/risk-matrices/1/pdf > http://127.0.0.1:8000/tecnico/risk-matrices/1/pdf

- Contrato Anual : Es donde tiene el cronograma para saber que tiene que completar, aca tendra una logica relacional con las tareas, visitas, etc

 - http://127.0.0.1:8000/risk-matrices/contrato-anual > http://127.0.0.1:8000/tecnico/risk-matrices/contrato-anual

 - Mapa de Calor Global : aca conforme, completa informacion de hallazgos, incidentes, etc alimenta el mapa de calor globar, esta en estapa de desarrollo

  -http://127.0.0.1:8000/risk-matrices/dashboard-riesgos > http://127.0.0.1:8000/tecnico/risk-matrices/dashboard-riesgos

  


- Dashboard
 http://127.00.0.1:8000/dashboard > http://127.0.0.1:8000/tecnico/dashboard