import { TestBed } from '@angular/core/testing';

import { QueryingService } from './querying.service';

describe('QueryingService', () => {
  let service: QueryingService;

  beforeEach(() => {
    TestBed.configureTestingModule({});
    service = TestBed.inject(QueryingService);
  });

  it('should be created', () => {
    expect(service).toBeTruthy();
  });
});
