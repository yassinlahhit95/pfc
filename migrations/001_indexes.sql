-- Missing composite indexes identified from query patterns in modelos/
-- Run once against yassjjzw_pfc

-- reclamaciones: unread count queries filter by idProfesor+leido and idEstudiante+leido
ALTER TABLE `reclamaciones`
  ADD INDEX `idx_recl_prof_leido` (`idProfesor`, `leido`),
  ADD INDEX `idx_recl_est_leido`  (`idEstudiante`, `leido`);

-- ejercicios: student listing queries filter by idCiclo AND publicado=1
ALTER TABLE `ejercicios`
  ADD INDEX `idx_ej_ciclo_pub` (`idCiclo`, `publicado`);
