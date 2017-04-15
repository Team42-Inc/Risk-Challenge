package oasix.middleware.model;

public class Port {
	private int port;
	private String defaultUsage;
	
	public Port(){
	}
	
	public Port(int port, String defaultUsage){
		this.port = port;
		this.defaultUsage = defaultUsage;
	}

	public int getPort() {
		return port;
	}

	public void setPort(int port) {
		this.port = port;
	}

	public String getDefaultUsage() {
		return defaultUsage;
	}

	public void setDefaultUsage(String defaultUsage) {
		this.defaultUsage = defaultUsage;
	}
	
	
}
