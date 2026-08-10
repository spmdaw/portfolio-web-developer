package examenFinal2Trimestre;

import java.util.ArrayList;
import java.util.Iterator;

public class Empresa {

	private ArrayList<Empleados> empresa;
	private double facturacion = 0;

	public Empresa() {
		empresa = new ArrayList<>();
	}

	public void añadirEmpleado(Empleados empleado) {
		empresa.add(empleado);
		
	}

	public boolean recibirOrden(String descripcion, double coste, int dificultad) {

		if (descripcion.contains("arreglar")) {

			for (Empleados empleados : empresa) {
				if (empleados instanceof Director) {

					return ((Director) empleados).mandarOrden(descripcion, 2, dificultad);

				}

			}

		}
		if (descripcion.contains("venta")) {
			for (Empleados empleados : empresa) {
				if (empleados instanceof Director) {
					return ((Director) empleados).mandarOrden(descripcion, 2, dificultad);
				}
			}

		}
		return false;

	}

}
