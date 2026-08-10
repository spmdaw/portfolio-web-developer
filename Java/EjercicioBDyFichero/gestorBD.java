package examen3Eva;

import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;
import java.time.LocalDate;
import java.util.ArrayList;
import java.util.List;

public class gestorBD {

	public static Connection conectar() {
		Connection conexion = null;
		try {
			conexion = DriverManager.getConnection(Constantes.DB_URL, Constantes.USUARIO, Constantes.CONTRASENA);
			System.out.println("Conexion exitosa");
			return conexion;

		} catch (SQLException e) {
			System.out.println("ERROR EN LA BASE DE DATOS  " + e.getMessage());
		}
		return null;

	}

	public List<Estudiante> obtenerEstudiantes() {

		Connection conexion = conectar();
		if (conexion != null) {
			try {
				String consulta = "SELECT * FROM estudiantes";
				Statement stmt = conexion.createStatement();
				ResultSet resultado = stmt.executeQuery(consulta);
				List<Estudiante> estudiantes = new ArrayList<>();
				while (resultado.next()) {

					int id = resultado.getInt("ID");
					String nombre = resultado.getString("nombre");
					java.sql.Date fechaSQL = resultado.getDate("fecha_nacimiento");
					LocalDate fecha_nac = fechaSQL.toLocalDate();
					double media = resultado.getDouble("nota_media");
					boolean promocionando = resultado.getBoolean("promocionado");
					Estudiante estudiante1 = new Estudiante(id, nombre, fecha_nac, media, promocionando);
					estudiantes.add(estudiante1);
				}
				conexion.close();
				stmt.close();
				resultado.close();
				return estudiantes;

			} catch (SQLException e) {
				System.out.println("ERROR EN LA BASE DE DATOS  " + e.getMessage());

			}
		}
		return null;

	}

	public void promocionarAlumnos(double nota_media) {

		Connection conexion = conectar();
		if (conexion != null) {
			try {
				String consulta = "SELECT id, nota_media FROM estudiantes";
				Statement stmt = conexion.createStatement();
				ResultSet resultado = stmt.executeQuery(consulta);

				String consulta1 = "UPDATE estudiantes SET promocionado=1 WHERE id=?";
				PreparedStatement preparar = conexion.prepareStatement(consulta1);

				while (resultado.next()) {
					int id = resultado.getInt("ID");
					double nota = resultado.getDouble("nota_media");
					if (nota >= nota_media) {
						preparar.setInt(1, id);
						int filasAfectadas = preparar.executeUpdate();

						if (filasAfectadas > 0) {
							System.out.println("Se actualizo correctamente. El estudiante con el ID: " + id
									+ " y su nota media ees de  :  " + nota);

						}

					}
				}

			} catch (SQLException e) {
				System.out.println("ERROR EN LA BASE DE DATOS  " + e.getMessage());
			}
		}

	}

}
