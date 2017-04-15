package oasix.middleware.model;

import java.util.ArrayList;
import java.util.Date;
import java.util.List;

import org.springframework.data.annotation.Id;
import org.springframework.data.elasticsearch.annotations.DateFormat;
import org.springframework.data.elasticsearch.annotations.Document;
import org.springframework.data.elasticsearch.annotations.Field;
import org.springframework.data.elasticsearch.annotations.FieldType;

import com.fasterxml.jackson.annotation.JsonFormat;

@Document(indexName="oasix-server", type="server")
public class ServerState  implements Comparable<ServerState> {
	@Id
	private String id;
	
	@Field( type = FieldType.Date, format = DateFormat.custom, pattern = "yyyy-MM-dd HH:mm:ss")
	@JsonFormat(shape = JsonFormat.Shape.STRING, pattern = "yyyy-MM-dd HH:mm:ss")
	private Date analysisDate;
	
    private String host;
    
    private String status;
    
    private String rate;
    
    private String trend;
    
    private Integer vulnerabilitiesCount;
  
    private Integer requiredUpdatesCount;
    
    @Field( type = FieldType.Object )
    SystemInformation systemInformation;
    
    @Field( type = FieldType.Object )
    List<Vulnerability> vulnerabilities = new ArrayList<Vulnerability>();

    @Field( type = FieldType.Object )
    List<RequiredUpdate> requiredUpdate = new ArrayList<RequiredUpdate>();
    
    @Field( type = FieldType.Object )
    List<Port> openPorts = new ArrayList<Port>();

    
    public ServerState() {
    }


	public String getId() {
		return id;
	}


	public void setId(String id) {
		this.id = id;
	}

	public Date getAnalysisDate() {
		return analysisDate;
	}


	public void setAnalysisDate(Date analysisDate) {
		this.analysisDate = analysisDate;
	}


	public String getHost() {
		return host;
	}


	public void setHost(String host) {
		this.host = host;
	}

	public String getStatus() {
		return status;
	}

	public void setStatus(String status) {
		this.status = status;
	}


	public String getRate() {
		return rate;
	}


	public void setRate(String rate) {
		this.rate = rate;
	}
	
	public String getTrend() {
		return trend;
	}


	public void setTrend(String trend) {
		this.trend = trend;
	}


	public Integer getVulnerabilitiesCount() {
		return vulnerabilitiesCount;
	}


	public void setVulnerabilitiesCount(Integer vulnerabilitiesCount) {
		this.vulnerabilitiesCount = vulnerabilitiesCount;
	}


	public Integer getRequiredUpdatesCount() {
		return requiredUpdatesCount;
	}


	public void setRequiredUpdatesCount(Integer requiredUpdatesCount) {
		this.requiredUpdatesCount = requiredUpdatesCount;
	}


	public SystemInformation getSystemInformation() {
		return systemInformation;
	}


	public void setSystemInformation(SystemInformation systemInformation) {
		this.systemInformation = systemInformation;
	}


	public List<Vulnerability> getVulnerabilities() {
		return vulnerabilities;
	}


	public void setVulnerabilities(List<Vulnerability> vulnerabilities) {
		this.vulnerabilities = vulnerabilities;
	}


	public List<RequiredUpdate> getRequiredUpdate() {
		return requiredUpdate;
	}


	public void setRequiredUpdate(List<RequiredUpdate> requiredUpdate) {
		this.requiredUpdate = requiredUpdate;
	}


	public List<Port> getOpenPorts() {
		return openPorts;
	}


	public void setOpenPorts(List<Port> openPorts) {
		this.openPorts = openPorts;
	}
	
	@Override
	  public int compareTo(ServerState o) {
		if(getAnalysisDate() == null){
			return -1;
		}
	    return getAnalysisDate().compareTo(o.getAnalysisDate());
	  }
}