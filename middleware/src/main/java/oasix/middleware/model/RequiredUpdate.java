package oasix.middleware.model;

public class RequiredUpdate {
	public String application;
	
	public String installedVersion;
	
	public String currentVersion;
	
	public RequiredUpdate(){
	}
	
	public RequiredUpdate(String application){
		this(application,"","");
	}
	
	public RequiredUpdate(String application, String installedVersion, String currentVersion){
		this.application = application;
		this.installedVersion = installedVersion;
		this.currentVersion = currentVersion;
	}
	
	public String getApplication() {
		return application;
	}

	public void setApplication(String application) {
		this.application = application;
	}

	public String getInstalledVersion() {
		return installedVersion;
	}

	public void setInstalledVersion(String installedVersion) {
		this.installedVersion = installedVersion;
	}

	public String getCurrentVersion() {
		return currentVersion;
	}

	public void setCurrentVersion(String currentVersion) {
		this.currentVersion = currentVersion;
	}
	
	
}
