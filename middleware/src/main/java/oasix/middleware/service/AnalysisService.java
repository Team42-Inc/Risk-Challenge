package oasix.middleware.service;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Date;
import java.util.List;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.scheduling.annotation.Async;
import org.springframework.stereotype.Component;

import oasix.middleware.model.Connection;
import oasix.middleware.model.Port;
import oasix.middleware.model.RequiredUpdate;
import oasix.middleware.model.ServerState;
import oasix.middleware.model.SystemInformation;
import oasix.middleware.model.Vulnerability;
import oasix.middleware.repository.ServerStateRepository;

@Component
public class AnalysisService {
	@Autowired
	ServerStateRepository serverStateRepository;
	
	@Autowired
	NiktoService niktoService;
	
	@Autowired
	VulnerabilityStatsService vulnerabilityStatsService;
	
	@Async
	public void analyse(String host, Date analysisTime){
		ServerState serverState = createServerState(analysisTime, host);
		serverStateRepository.save(serverState);
	}
	
	private ServerState createServerState(Date analysisTime, String host){
		ServerState serverState = new ServerState();
		serverState.setHost(host);
		serverState.setAnalysisDate(analysisTime);
		
		SimpleDateFormat df = new SimpleDateFormat("yyyy-MM-dd-HH:mm:ss");
		String id = host + "-" + df.format(analysisTime);
		serverState.setId(id);
		
		SystemInformation systemInformation = new SystemInformation();
		systemInformation.setOperatingSystem("Linux");
		systemInformation.setVersion("Ubuntu 16.02");
		serverState.setSystemInformation(systemInformation);
		
		List<Port> openPorts = new ArrayList<>();
		openPorts.add(new Port(8080,"TCP", "open", "http"));
		openPorts.add(new Port(22,"TCP", "open", "ssh"));
		serverState.setOpenPorts(openPorts);
		
		List<Vulnerability> vulnerabilities = new ArrayList<>();
		
		vulnerabilities.addAll(niktoService.scan(host));
		
		vulnerabilities.add(new Vulnerability("APPLICATION", "MAJEUR", "Sql injection", "Lorem ipsum"));
		vulnerabilities.add(new Vulnerability("APPLICATION", "MAJEUR", "CSRF", "Lorem ipsum"));
		vulnerabilities.add(new Vulnerability("ADMINISTRATION", "CRITIQUE", "Root kit", "Lorem ipsum"));
		serverState.setVulnerabilities(vulnerabilities);
		
		List<RequiredUpdate> requiredUpdates = new ArrayList<>();
		requiredUpdates.add(new RequiredUpdate("mysql","2","14"));
		requiredUpdates.add(new RequiredUpdate("php","3","5"));
		serverState.setRequiredUpdate(requiredUpdates);
		
		serverState.setStatus("OK");
		serverState.setRate(""+evaluate(serverState));
		serverState.setTrend("UP");
		
		vulnerabilityStatsService.aggregate(serverState);
		
		return serverState;
	}
	
	private Integer evaluate(ServerState serverState){
		return 70;
	}

	public void cleanup() {
		serverStateRepository.delete(serverStateRepository.findAll());
	}
}
