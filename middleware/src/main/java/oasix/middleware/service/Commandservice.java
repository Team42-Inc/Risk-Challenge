package oasix.middleware.service;

import java.util.ArrayList;
import java.util.Date;
import java.util.List;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Component;

import oasix.middleware.model.Command;
import oasix.middleware.repository.CommandRepository;

@Component
public class Commandservice {
	@Autowired
	CommandRepository commandRepository;
		
	public void aggregate(String host){
		//cleanup(host);
		
		List<Command> stats = new ArrayList<>();
		stats.add(new Command(host, new Date(), "admin", "ls"));
		stats.add(new Command(host, new Date(), "user1" ,"chmod 777"));
		stats.add(new Command(host,new Date(), "user2", "cp *"));
		
		
		commandRepository.save(stats);
	}

	private void cleanup(String host) {
		List<Command> commands = commandRepository.findByHost(host);
		commandRepository.delete(commands);
	}
	
	public void cleanup() {
		commandRepository.delete(commandRepository.findAll());
	}
}
