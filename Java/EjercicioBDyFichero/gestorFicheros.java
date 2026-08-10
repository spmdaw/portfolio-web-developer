package examen3Eva;

import java.io.File;
import java.io.FileWriter;
import java.io.IOException;
import java.util.Map;

public class gestorFicheros {

	public void exportarNumEstudiantesPorCalificacion(Map<String, Integer> mapa) {

		try {

			File carpeta = new File("estadisticas");

			if (!carpeta.exists()) {
				carpeta.mkdir();
				System.out.println("SE CREO LA CARPETA...");
			}
			File fichero = new File(Constantes.rutaFichero);
			FileWriter escritor = new FileWriter(fichero, true);

			for (Map.Entry<String, Integer> entry : mapa.entrySet()) {
				String key = entry.getKey();
				Integer val = entry.getValue();

				String linea = key + " : " + val;
				escritor.write(linea + "\n");

			}

			escritor.close();

		} catch (IOException e) {
			System.out.println("ERROR EN EL FICHERO " + e.getMessage());
		}

	}

}
