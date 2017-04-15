package oasix.middleware.repository;

import java.util.List;

import org.springframework.data.elasticsearch.repository.ElasticsearchCrudRepository;

import oasix.middleware.model.Command;
import oasix.middleware.model.Log;

public interface LogsRepository extends ElasticsearchCrudRepository<Log, String> {
	List<Log> findBySysloghost(String host);
	
	List<Log> findByHost(String host);
}