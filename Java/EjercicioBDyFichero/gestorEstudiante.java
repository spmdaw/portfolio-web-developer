package examen3Eva;

import java.sql.Connection;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;
import java.util.HashMap;
import java.util.HashSet;
import java.util.List;
import java.util.Map;
import java.util.Set;

public class gestorEstudiante {

	public Set<String> obtenerNombresSinRepeticion() {

		Connection conexion = gestorBD.conectar();
		if (conexion != null) {
			try {
				String consulta = "SELECT nombre FROM estudiantes";
				Statement stmt = conexion.createStatement();
				ResultSet resultado = stmt.executeQuery(consulta);
				Set<String> nombresEstudiantes = new HashSet<>();
				while (resultado.next()) {
					String nombre = resultado.getString("nombre");
					nombresEstudiantes.add(nombre);

				}
				return nombresEstudiantes;

			} catch (SQLException e) {
				System.out.println("ERROR EN LA BASE DE DATOS " + e.getMessage());
			}
		}
		return null;

	}

	public Map<String, Integer> obtenerNumEstudiantesPorCalificacion() {

		Connection conexion = gestorBD.conectar();
		if (conexion != null) {
			try {
				String consulta = "SELECT id,nota_media FROM estudiantes";
				Statement stmt = conexion.createStatement();
				ResultSet resultado = stmt.executeQuery(consulta);
				Map<String, Integer> mapa = new HashMap<>();

				int sobresaliente = 0;
				int notable = 0;
				int bien = 0;
				int suficiente = 0;
				int insuficiente = 0;
				int noPre = 0;

				while (resultado.next()) {
					double nota = resultado.getDouble("nota_media");

					if (nota >= 9) {
						sobresaliente++;
						mapa.put("SOBRESALIENTE", sobresaliente);
					}
					if (nota >= 7 && nota < 9) {
						notable++;
						mapa.put("NOTABLE", notable);
					}
					if (nota >= 6 && nota < 7) {
						bien++;
						mapa.put("BIEN", bien);
					}
					if (nota >= 5 && nota < 6) {
						suficiente++;
						mapa.put("SUFICIENTE", suficiente);
					}
					if (nota < 5) {
						insuficiente++;
						mapa.put("INSUFICIENTE", insuficiente);
					}
					if (nota < -1) {
						noPre++;
						mapa.put("NO PRESENTADO", noPre);
					}

				}
				return mapa;

			} catch (SQLException e) {
				System.out.println("ERROR EN LA BASE DE DATOS  " + e.getMessage());
			}
		}
		return null;

	}

	public double obtenerPromedioEdades(List<Estudiante> estudiantes) {

		double suma = 0;
		for (Estudiante estudiante2 : estudiantes) {

			int edad = ((Estudiante) estudiante2).getEdad();

			suma += edad;

		}
		double media = suma / estudiantes.size();

		return media;

	}

}
