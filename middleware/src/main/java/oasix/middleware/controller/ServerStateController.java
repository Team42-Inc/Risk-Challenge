package oasix.middleware.controller;

import java.util.ArrayList;
import java.util.Collections;
import java.util.Comparator;
import java.util.Date;
import java.util.List;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RequestParam;
import org.springframework.web.bind.annotation.RestController;

import oasix.middleware.model.Command;
import oasix.middleware.model.ConnectionStats;
import oasix.middleware.model.ServerState;
import oasix.middleware.model.VulnerabilityStats;
import oasix.middleware.repository.CommandRepository;
import oasix.middleware.repository.ConnectionStatsRepository;
import oasix.middleware.repository.ServerStateRepository;
import oasix.middleware.repository.VulnerabilityStatsRepository;
import oasix.middleware.service.AggregationService;

@RestController
@RequestMapping("/servers")
public class ServerStateController {
	@Autowired
	ServerStateRepository serverStateRepository;
	
	@Autowired
	AggregationService aggregationService;
	
	@Autowired
	ConnectionStatsRepository connectionStatsRepository;
	
	@Autowired
	VulnerabilityStatsRepository vulnerabilityStatsRepository;
	
	@Autowired
	CommandRepository commandRepository;
	
	@GetMapping("/")
	public Iterable<ServerState> findAll(){
		 Iterable<ServerState> queryResult = serverStateRepository.findAll();
		 List<ServerState> results = new ArrayList<>();
		 for(ServerState serverState : queryResult){
			 results.add(serverState);
		 }
		 return results;
	}
	
	@GetMapping("/state")
	public ServerState get(@RequestParam String host){
		List<ServerState> results = serverStateRepository.findByHost(host);
		if(results.size()==0){
			return null;
		}
		Comparator<ServerState> cmp = Comparator.comparing(ServerState::getAnalysisDate);
		ServerState lastState = Collections.max(results);
		return lastState;	
	}
	
	@GetMapping("/history")
	public Iterable<ServerState> getHistory(@RequestParam String host){
		return serverStateRepository.findAll();
	}
	
	@GetMapping("/metrics/connections")
	public Iterable<ConnectionStats> getConnections(@RequestParam String host){
		return connectionStatsRepository.findAll();
	}
	
	@GetMapping("/metrics/connections/cleanup")
	public void cleanupConnections(@RequestParam String host){
		List<ConnectionStats> stats = connectionStatsRepository.findByHost(host);
		connectionStatsRepository.delete(stats);
	}
	
	@GetMapping("/metrics/vulnerabilities")
	public Iterable<VulnerabilityStats> getVulnerabilities(@RequestParam String host){
		return vulnerabilityStatsRepository.findByHost(host);
	}
	
	@GetMapping("/metrics/vulnerabilities/cleanup")
	public void cleanupVulnerabilities(@RequestParam String host){
		List<VulnerabilityStats> stats = vulnerabilityStatsRepository.findByHost(host);
		vulnerabilityStatsRepository.delete(stats);
	}
	
	@GetMapping("/commands")
	public Iterable<Command> getCommandHistory(@RequestParam String host){
		return commandRepository.findByHost(host);
	}
	
	@PostMapping("/analyse")
	public void analyse(@RequestParam String host){
		aggregationService.analyse(host, new Date());
	}
}
