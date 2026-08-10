package examen3Eva;

import java.util.List;
import java.util.Map;
import java.util.Set;

public class Main {

	
	public static void main(String[] args) {
		
		gestorBD gestor= new gestorBD();
		List<Estudiante> array= gestor.obtenerEstudiantes();
		
		for (Estudiante estudiante : array) {
			System.out.println(estudiante);
		}
		
		gestor.promocionarAlumnos(7);
		gestorEstudiante gestorEstudiante= new gestorEstudiante();
		Set<String> nombres=   gestorEstudiante.obtenerNombresSinRepeticion();
		
		for (String string : nombres) {
			System.out.println(string);
		}
		
		
		
		
		 Map<String, Integer>   mapa=  gestorEstudiante.obtenerNumEstudiantesPorCalificacion();
		 for (Map.Entry<String, Integer> entry : mapa.entrySet()) {
			String key = entry.getKey();
			Integer val = entry.getValue();
			System.out.println(key +"  --->  " +val);
			
		}
		
		System.out.println("EL PROMEDIO DE EDADES ES : " +gestorEstudiante.obtenerPromedioEdades(array)); 
		
		
		
		
		
		gestorFicheros gestorFichero= new gestorFicheros();
		gestorFichero.exportarNumEstudiantesPorCalificacion(mapa);

		
		
		
		
		
	}
}
