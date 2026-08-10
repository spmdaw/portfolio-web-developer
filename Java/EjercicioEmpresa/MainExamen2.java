package examenFinal2Trimestre;
public class MainExamen2 {
	public static void main(String[] args) {
		
		
		Empresa empresa = new Empresa();

		Tecnico tecnico1 = new Tecnico(1, "Juan");
		Vendedor vendedor1 = new Vendedor(2, "Pedro");

		
		CoordinadorTecnico coordinadorTecnico1 = new CoordinadorTecnico(3, "Carlos");
		coordinadorTecnico1.añadirEmpleadosTecnicos(tecnico1);
		CoordinadorVentas coordinadorVentas1 = new CoordinadorVentas(4, "Lucia");
		coordinadorVentas1.añadirEmpleadosVentas(vendedor1);

		Director director = new Director(5, "Ana", coordinadorTecnico1, coordinadorVentas1);

		empresa.añadirEmpleado(tecnico1);
		empresa.añadirEmpleado(vendedor1);
		empresa.añadirEmpleado(coordinadorTecnico1);
		empresa.añadirEmpleado(coordinadorVentas1);
		empresa.añadirEmpleado(director);

		// Mandar una orden
		empresa.recibirOrden("arreglar computadora", 500.0, 2); // Tarea técnica
		empresa.recibirOrden("venta de producto", 200.0, 3); // Tarea de ventas

	}
}
