---
description: futuras implementaciones (iniciar cuando se lo pida)
---

Con esa alerta que propones, el sistema encapsula las dos validaciones de seguridad de manera transparente para el técnico:
El Anclaje de Tiempo y Espacio (Con Señal): Antes de subir a la montaña, la app le lanza la alerta para que inicie la visita. En ese momento, el servidor registra la hora inmutable y el sistema exige la captura de latitud y longitud inicial
.
Ejecución en Modo PWA (Sin Señal): El técnico realiza los checklists. La PWA guarda temporalmente la hora local del dispositivo para cada registro.
Sincronización y Validación (Al recuperar Señal): Cuando el dispositivo vuelve a conectarse, hace dos cosas:
Valida la Hora: Realiza la prueba de "Time-Drift" que discutimos, comparando que el reloj del teléfono no haya sido adulterado durante el viaje.
Valida el Lugar (Proof of Presence): El backend toma las coordenadas GPS registradas en la entrada y en la salida, y aplica la Fórmula matemática de Haversine para calcular la distancia exacta
. Si la distancia calculada es menor o igual al radio_tolerancia_metros configurado en la sede (por ejemplo, 500 metros), el sistema marca automáticamente la jornada como Verificada por GPS
.
De esta forma, mantienes la operatividad fluida que exige el trabajo en la minería, pero con un blindaje anti-fraude absoluto.
Sabiendo que esta capa de PWA y GPS la dejaremos para la etapa final,