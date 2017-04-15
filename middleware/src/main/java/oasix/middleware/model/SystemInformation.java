package oasix.middleware.model;

public class SystemInformation {
	private String operatingSystem;
	
	private String version;

	public SystemInformation(){
	}
	
	public String getOperatingSystem() {
		return operatingSystem;
	}

	public void setOperatingSystem(String operatingSystem) {
		this.operatingSystem = operatingSystem;
	}

	public String getVersion() {
		return version;
	}

	public void setVersion(String version) {
		this.version = version;
	}
}
